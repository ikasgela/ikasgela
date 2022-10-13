<?php

namespace App\Http\Controllers;

use App\Jobs\ForkGiteaRepo;
use App\Models\Actividad;
use App\Models\Curso;
use App\Models\IntellijProject;
use App\Models\MarkdownText;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Traits\ClonarRepoGitea;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Auth;
use Cache;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Log;

class IntellijProjectController extends Controller
{
    use ClonarRepoGitea;
    use PaginarUltima;
    use FiltroCurso;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor|admin', ['except' => ['fork', 'is_forking', 'download']]);
    }

    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $intellij_projects = $this->filtrar_por_curso($request, IntellijProject::class)->get();

        return view('intellij_projects.index', compact(['intellij_projects', 'cursos']));
    }

    public function create()
    {
        return view('intellij_projects.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'repositorio' => 'required',
            'titulo' => 'required',
            'host' => 'required',
        ]);

        $request->merge([
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        IntellijProject::create($request->all());

        return retornar();
    }

    public function show(IntellijProject $intellij_project)
    {
        abort(404);
    }

    public function edit(IntellijProject $intellij_project)
    {
        $repositorio = $intellij_project->repository();

        return view('intellij_projects.edit', compact(['intellij_project', 'repositorio']));
    }

    public function update(Request $request, IntellijProject $intellij_project)
    {
        $this->validate($request, [
            'repositorio' => 'required',
            'titulo' => 'required',
            'host' => 'required',
        ]);

        $intellij_project->update($request->all());

        if (!$intellij_project->isForked()) {
            Log::debug("IntellijProjectController - update() - Repositorio plantilla borrado de caché: ", ['key' => $intellij_project->templateCacheKey(), 'repo' => $intellij_project->repositorio]);
            Cache::forget($intellij_project->templateCacheKey());
        } else {
            Log::debug("IntellijProjectController - update() - Repositorio fork borrado de caché: ", ['key' => $intellij_project->cacheKey(), 'repo' => $intellij_project->repositorio]);
            Cache::forget($intellij_project->cacheKey());
        }

        return retornar();
    }

    public function destroy(IntellijProject $intellij_project)
    {
        $intellij_project->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $intellij_projects = $actividad->intellij_projects()->get();

        $subset = $intellij_projects->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(IntellijProject::where('curso_id', $curso_actual)->whereNotIn('id', $subset));

        return view('intellij_projects.actividad', compact(['intellij_projects', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = IntellijProject::find($recurso_id);
            $actividad->intellij_projects()->attach($recurso, ['orden' => Str::orderedUuid()]);
        }

        return redirect(route('intellij_projects.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, IntellijProject $intellij_project)
    {
        $actividad->intellij_projects()->detach($intellij_project);
        return redirect(route('intellij_projects.actividad', ['actividad' => $actividad->id]));
    }

    public function fork(Actividad $actividad, IntellijProject $intellij_project)
    {
        $proyecto = $actividad->intellij_projects()->find($intellij_project->id);

        $proyecto->setForkStatus(1);  // Forking

        if (!App::environment('testing')) {
            $team_users = [];
            if ($actividad->hasEtiqueta('trabajo en equipo')) {
                $compartidas = Tarea::where('actividad_id', $actividad->id)->get();
                foreach ($compartidas as $compartida) {
                    array_push($team_users, $compartida->user);
                }
            }
            ForkGiteaRepo::dispatch($actividad, $intellij_project, Auth::user(), $team_users);
        } else {
            ForkGiteaRepo::dispatchSync($actividad, $intellij_project, Auth::user());
        }

        return redirect(route('users.home'));
    }

    public function is_forking(Actividad $actividad, IntellijProject $intellij_project, Request $request)
    {
        $proyecto = $actividad->intellij_projects()->find($intellij_project->id);

        return $proyecto->getForkStatus();  // 0 sin clonar, 1 clonando, 2 completado, 3 error
    }

    public function copia()
    {
        // Solo los proyectos del root

        $proyectos = GiteaClient::repos_usuario('root');

        return view('intellij_projects.copia', compact('proyectos'));
    }

    public function borrar($id)
    {
        GiteaClient::borrar_repo($id);

        return back();
    }

    public function clonar(Request $request)
    {
        $origen = $request->input('origen');    // root/origen
        $destino = $request->input('destino');  // root/copia
        $nombre = $request->input('nombre');    // Nuevo proyecto

        $split = explode("/", $destino);

        $usuario = $split[0] ?? '';
        $ruta = $split[1] ?? '';

        // Guardar en la sesión los datos para prerellenar el formulario
        session([
            'intellij_origen' => $origen,
            'intellij_destino' => $destino,
        ]);

        try {
            $proyecto = GiteaClient::repo($origen);

            if (empty($usuario)) {
                $usuario = $proyecto['owner'];
            }

            if (empty($ruta)) {
                $ruta = $proyecto['name'];
            }

            if (empty($nombre)) {
                $nombre = $proyecto['description'];
            }

            // Verificar que sea plantilla, si no, convertirlo
            if (!$proyecto['template']) {
                GiteaClient::template($proyecto['owner'], $proyecto['name'], true);
            }

            $clonado = $this->clonar_repositorio($proyecto['path_with_namespace'], $usuario, $ruta, $nombre);

            GiteaClient::template($clonado['owner'], $clonado['name'], true);

            // Crear el recurso asociado al nuevo repositorio
            $abrir_con = "";
            switch (request('recurso_type')) {
                case 'intellij_project_idea':
                    $abrir_con = 'idea';
                    break;
                case 'intellij_project_phpstorm':
                    $abrir_con = 'phpstorm';
                    break;
                case 'intellij_project_datagrip':
                    $abrir_con = 'datagrip';
                    break;
            }

            switch (request('recurso_type')) {
                case 'intellij_project_idea':
                case 'intellij_project_phpstorm':
                case 'intellij_project_datagrip':
                case 'intellij_project':
                    IntellijProject::create([
                        'titulo' => $clonado['description'],
                        'descripcion' => 'Clona el repositorio y abre el proyecto en IntelliJ. El enunciado está dentro del proyecto, en el archivo README.md.',
                        'repositorio' => $clonado['path_with_namespace'],
                        'host' => 'gitea',
                        'open_with' => $abrir_con,
                        'curso_id' => Auth::user()->curso_actual()->id,
                    ]);
                    break;
                case 'markdown_text':
                    MarkdownText::create([
                        'titulo' => $clonado['description'],
                        'repositorio' => $clonado['path_with_namespace'],
                        'host' => 'gitea',
                        'curso_id' => Auth::user()->curso_actual()->id,
                        'rama' => 'master',
                        'archivo' => 'README.md',
                    ]);
                    break;
                default:
                    // Ok, no crear nada
            }
        } catch (\Exception $e) {
            Log::error($e);
        }

        return redirect(route('intellij_projects.copia'));
    }

    public function lock(IntellijProject $intellij_project, Actividad $actividad)
    {
        $proyecto = $actividad->intellij_projects()->wherePivot('intellij_project_id', $intellij_project->id)->first();

        $proyecto->archive();

        return back();
    }

    public function unlock(IntellijProject $intellij_project, Actividad $actividad)
    {
        $proyecto = $actividad->intellij_projects()->wherePivot('intellij_project_id', $intellij_project->id)->first();

        $proyecto->unarchive();

        return back();
    }

    public function download(IntellijProject $intellij_project)
    {
        $repositorio = $intellij_project->repository();

        return response()->streamDownload(function () use ($repositorio) {
            echo GiteaClient::download($repositorio['owner'], $repositorio['name'], 'master.zip');
        }, $repositorio['name'] . '.zip');
    }

    public function descargar_repos(Request $request)
    {
        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        if ($request->has('unidad_id')) {

            $fecha = now()->format('Ymd-His');

            $unidad = Unidad::findOrFail($request->get('unidad_id'));

            $fichero = $fecha . "-" . $unidad->slug . ".sh";
            $datos = "#!/bin/sh\n\n";

            $curso_actual = Curso::find(setting_usuario('curso_actual'));

            if ($curso_actual != null) {

                $datos .= "mkdir '" . $fecha . "-" . $unidad->slug . "'\n";
                $datos .= "cd '" . $fecha . "-" . $unidad->slug . "'\n";
                $datos .= "\n";

                $datos .= "RUTA=\"\$PWD\"\n";
                $datos .= "\n";

                $alumnos = $curso_actual->users()->rolAlumno()->noBloqueado()->get();

                $actividades_revisar = [];

                foreach ($alumnos as $alumno) {

                    $actividades = $alumno->actividades()->where('unidad_id', $unidad->id)->get();

                    foreach ($actividades as $actividad) {

                        $datos .= "mkdir -p '" . $actividad->slug . "'\n";
                        $datos .= "cd '" . $actividad->slug . "'\n";

                        foreach ($actividad->intellij_projects()->get() as $project) {
                            if (Str::length($project->pivot->fork) > 0) {
                                $datos .= "git clone ";
                                $repositorio = GiteaClient::repo($project->pivot->fork);
                                $datos .= "'" . $repositorio['http_url_to_repo'] . "'";
                                $datos .= " $alumno->username-" . $repositorio['name'] . "\n";
                            }
                        }

                        $datos .= "cd \$RUTA\n";

                        $actividades_revisar = array_unique(array_merge($actividades_revisar, [$actividad->slug]), SORT_REGULAR);
                    }
                }

                $datos .= "\n";

                foreach ($actividades_revisar as $actividad) {
                    $datos .= "cd $actividad\n";
                    $datos .= "jplag\n";
                    $datos .= "cd \$RUTA\n";
                    $datos .= "\n";
                }

                $datos .= "cd ..";
                $datos .= "\n";

                return response()->streamDownload(function () use ($datos) {
                    echo $datos;
                }, $fichero);
            }
        }

        return view('intellij_projects.descargar', compact(['unidades']));
    }

    public function descargar_plantillas(Request $request)
    {
        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        if ($request->has('unidad_id')) {

            $fecha = now()->format('Ymd-His');

            $unidad = Unidad::findOrFail($request->get('unidad_id'));

            $fichero = $fecha . "-" . $unidad->slug . ".sh";
            $datos = "#!/bin/sh\n\n";

            $curso_actual = Curso::find(setting_usuario('curso_actual'));

            if ($curso_actual != null) {

                $actividades = $unidad->actividades()->plantilla()->get();

                foreach ($actividades as $actividad) {

                    foreach ($actividad->intellij_projects()->get() as $project) {
                        $datos .= "git clone ";
                        $repositorio = GiteaClient::repo($project->repositorio);
                        $datos .= "'" . $repositorio['http_url_to_repo'] . "'";
                        $datos .= "\n";
                    }
                }

                return response()->streamDownload(function () use ($datos) {
                    echo $datos;
                }, $fichero);
            }
        }

        return view('intellij_projects.descargar', compact(['unidades']));
    }

    public function toggle_titulo_visible(Actividad $actividad, IntellijProject $intellij_project)
    {
        $pivote = $intellij_project->pivote($actividad);

        $pivote->titulo_visible = !$pivote->titulo_visible;
        $pivote->save();

        return back();
    }

    public function toggle_descripcion_visible(Actividad $actividad, IntellijProject $intellij_project)
    {
        $pivote = $intellij_project->pivote($actividad);

        $pivote->descripcion_visible = !$pivote->descripcion_visible;
        $pivote->save();

        return back();
    }
}
