<?php

namespace App\Http\Controllers;

use App;
use App\Actividad;
use App\Gitea\GiteaClient;
use App\IntellijProject;
use App\Jobs\ForkGiteaRepo;
use App\Jobs\ForkGitLabRepo;
use App\Traits\ClonarRepoGitea;
use App\Traits\PaginarUltima;
use Auth;
use BadMethodCallException;
use Cache;
use GitLab;
use Illuminate\Http\Request;
use Log;

class IntellijProjectController extends Controller
{
    use ClonarRepoGitea;
    use PaginarUltima;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor', ['except' => ['fork', 'is_forking']]);
    }

    public function index()
    {
        $intellij_projects = IntellijProject::all();

        return view('intellij_projects.index', compact('intellij_projects'));
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

        IntellijProject::create($request->all());

        return retornar();
    }

    public function show(IntellijProject $intellij_project)
    {
        return abort(501, __('Not implemented.'));
    }

    public function edit(IntellijProject $intellij_project)
    {
        return view('intellij_projects.edit', compact('intellij_project'));
    }

    public function update(Request $request, IntellijProject $intellij_project)
    {
        $this->validate($request, [
            'repositorio' => 'required',
            'titulo' => 'required',
            'host' => 'required',
        ]);

        $intellij_project->update($request->all());

        Cache::forget($intellij_project->cacheKey());

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

        $disponibles = $this->paginate_ultima(IntellijProject::whereNotIn('id', $subset));

        return view('intellij_projects.actividad', compact(['intellij_projects', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = IntellijProject::find($recurso_id);
            $actividad->intellij_projects()->attach($recurso);
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
            if ($intellij_project->host == 'gitlab') {
                ForkGitLabRepo::dispatch($actividad, $intellij_project, Auth::user()); //->delay(10);
            } else {
                ForkGiteaRepo::dispatch($actividad, $intellij_project, Auth::user());
            }
        } else {
            if ($intellij_project->host == 'gitlab') {
                ForkGitLabRepo::dispatchNow($actividad, $intellij_project, Auth::user());
            } else {
                ForkGiteaRepo::dispatchNow($actividad, $intellij_project, Auth::user());
            }
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

        if (config('ikasgela.gitlab_enabled')) {
            $proyectos = GitLab::projects()->all([
                'membership' => true
            ]);
        }

        if (config('ikasgela.gitea_enabled')) {
            $proyectos = GiteaClient::repos_usuario('root');
        }

        return view('intellij_projects.copia', compact('proyectos'));
    }

    public function borrar($id)
    {
        if (config('ikasgela.gitlab_enabled')) {
            GitLab::projects()->remove($id);
        }

        if (config('ikasgela.gitea_enabled')) {
            GiteaClient::borrar_repo($id);
        }

        return back();
    }

    public function duplicar(Request $request)
    {
        $origen = $request->input('origen');
        $destino = $request->input('destino');
        $ruta = $request->input('ruta');
        $nombre = $request->input('nombre');

        // Guardar en la sesión los datos para prerellenar el formulario
        session([
            'intellij_origen' => $origen,
            'intellij_destino' => $destino
        ]);

        try {
            $proyecto = GiteaClient::repo($origen);

            if (empty($ruta)) {
                $ruta = $proyecto['name'];
            }

            if (empty($nombre)) {
                $nombre = $proyecto['description'];
            }

            $this->clonar_repositorio($proyecto['path_with_namespace'], $destino, $ruta, $nombre);
        } catch (\Exception $e) {
            Log::error($e);
        }

        return redirect(route('intellij_projects.copia'));
    }

    public function testGitLab(Request $request)
    {
        $n = 0 + $request->input('n');

        for ($i = 0; $i < $n; $i++) {
            $actividad = factory(Actividad::class)->create();
            $intellij_project = factory(IntellijProject::class)->create();

            $actividad->intellij_projects()->attach($intellij_project, ['is_forking' => true]);

            Auth::user()->actividades()->attach($actividad);

            ForkGitLabRepo::dispatch($actividad, $intellij_project, Auth::user(), true);
        }

        return 'ok';
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
}
