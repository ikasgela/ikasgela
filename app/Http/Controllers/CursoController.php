<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFile;
use App\Jobs\BorrarCurso;
use App\Jobs\ImportCurso;
use App\Models\Category;
use App\Models\Curso;
use App\Models\User;
use App\Traits\TareaBienvenida;
use Ikasgela\Gitea\GiteaClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as SystemFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToReadFile;
use ZipArchive;

class CursoController extends Controller
{
    use TareaBienvenida;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['matricular', 'curso_actual']);
    }

    public function index()
    {
        $cursos = Curso::all();

        $usuario = Auth::user();

        return view('cursos.index', compact(['cursos', 'usuario']));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('cursos.create', compact(['categories']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'nombre' => 'required',
            'plazo_actividad' => 'required',
            'gitea_organization' => 'max:40',
        ]);

        $curso = Curso::create([
            'category_id' => request('category_id'),
            'nombre' => request('nombre'),
            'descripcion' => request('descripcion'),
            'slug' => Str::length(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('nombre')),
            'gitea_organization' => Str::length(request('gitea_organization')) > 0
                ? Str::slug(request('gitea_organization'))
                : Str::limit(Str::slug(request('nombre')), 40, ''),
            'tags' => request('tags'),
            'matricula_abierta' => $request->has('matricula_abierta'),
            'qualification_id' => request('qualification_id'),
            'max_simultaneas' => request('max_simultaneas'),
            'plazo_actividad' => request('plazo_actividad'),
            'fecha_inicio' => request('fecha_inicio'),
            'fecha_fin' => request('fecha_fin'),
            'minimo_entregadas' => request('minimo_entregadas'),
            'minimo_competencias' => request('minimo_competencias'),
            'minimo_examenes' => request('minimo_examenes'),
            'minimo_examenes_finales' => request('minimo_examenes_finales'),
            'examenes_obligatorios' => $request->has('examenes_obligatorios'),
            'maximo_recuperable_examenes_finales' => request('maximo_recuperable_examenes_finales'),
            'progreso_visible' => $request->has('progreso_visible'),
            'silence_notifications' => $request->has('silence_notifications'),
            'normalizar_nota' => $request->has('normalizar_nota'),
            'ajuste_proporcional_nota' => $request->input('ajuste_proporcional_nota'),
            'mostrar_calificaciones' => $request->has('mostrar_calificaciones'),
        ]);

        GiteaClient::organization($curso->gitea_organization, $curso->nombre);

        return retornar();
    }

    public function show(Curso $curso)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Curso $curso
     * @return Response
     */
    public function edit(Curso $curso)
    {
        $categories = Category::orderBy('name')->get();
        $qualifications = $curso->qualifications()->orderBy('name')->get();

        return view('cursos.edit', compact(['curso', 'categories', 'qualifications']));
    }

    public function update(Request $request, Curso $curso)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'nombre' => 'required',
            'plazo_actividad' => 'required',
            'gitea_organization' => 'max:40',
        ]);

        $curso->update([
            'category_id' => request('category_id'),
            'nombre' => request('nombre'),
            'descripcion' => request('descripcion'),
            'slug' => Str::length(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('nombre')),
            'gitea_organization' => Str::length(request('gitea_organization')) > 0
                ? Str::slug(request('gitea_organization'))
                : Str::limit(Str::slug(request('nombre')), 40, ''),
            'tags' => request('tags'),
            'matricula_abierta' => $request->has('matricula_abierta'),
            'qualification_id' => request('qualification_id'),
            'max_simultaneas' => request('max_simultaneas'),
            'plazo_actividad' => request('plazo_actividad'),
            'fecha_inicio' => request('fecha_inicio'),
            'fecha_fin' => request('fecha_fin'),
            'minimo_entregadas' => request('minimo_entregadas'),
            'minimo_competencias' => request('minimo_competencias'),
            'minimo_examenes' => request('minimo_examenes'),
            'minimo_examenes_finales' => request('minimo_examenes_finales'),
            'examenes_obligatorios' => $request->has('examenes_obligatorios'),
            'maximo_recuperable_examenes_finales' => request('maximo_recuperable_examenes_finales'),
            'progreso_visible' => $request->has('progreso_visible'),
            'silence_notifications' => $request->has('silence_notifications'),
            'tarea_bienvenida_id' => request('tarea_bienvenida_id'),
            'normalizar_nota' => $request->has('normalizar_nota'),
            'ajuste_proporcional_nota' => $request->input('ajuste_proporcional_nota'),
            'mostrar_calificaciones' => $request->has('mostrar_calificaciones'),
        ]);

        GiteaClient::organization($curso->gitea_organization, $curso->nombre);

        return retornar();
    }

    public function destroy(Curso $curso)
    {
        BorrarCurso::dispatch($curso);
        return back();
    }

    public function export(Curso $curso)
    {
        // Crear el directorio temporal
        $directorio = '/' . Str::uuid() . '/';
        Storage::disk('temp')->makeDirectory($directorio);
        Storage::disk('temp')->makeDirectory($directorio . '/repositorios/');
        Storage::disk('temp')->makeDirectory($directorio . '/markdown/');
        $ruta = Storage::disk('temp')->path($directorio);

        // Curso
        $this->exportarFicheroJSON($ruta, 'curso.json', $curso);

        // Unidad
        $this->exportarFicheroJSON($ruta, 'unidades.json', $curso->unidades()->orderBy('orden')->get());

        // Actividad
        $this->exportarFicheroJSON($ruta, 'actividades.json', $curso->actividades()->plantilla()->orderBy('orden')->get());

        // Qualification
        $this->exportarFicheroJSON($ruta, 'qualifications.json', $curso->qualifications()->plantilla()->get());

        // Skill
        $this->exportarFicheroJSON($ruta, 'skills.json', $curso->skills);

        // Qualification "*" -- "*" Skill
        $datos = DB::table('qualification_skill')
            ->join('qualifications', 'qualification_skill.qualification_id', '=', 'qualifications.id')
            ->where('qualifications.curso_id', '=', $curso->id)
            ->where('qualifications.template', '=', true)
            ->select("qualification_skill.*")
            ->orderBy('orden')
            ->get();
        $this->exportarFicheroJSON($ruta, 'qualification_skill.json', $datos);

        // IntellijProject
        $this->exportarFicheroJSON($ruta, 'intellij_projects.json', $curso->intellij_projects);
        $this->exportarRepositorios($ruta, $curso);

        // Actividad "*" -- "*" IntellijProject
        $this->exportarRelacionJSON($ruta, $curso, 'intellij_project');

        // MarkdownText
        $this->exportarFicheroJSON($ruta, 'markdown_texts.json', $curso->markdown_texts);
        $this->exportarMarkdown($ruta, $curso);

        // Actividad "*" -- "*" MarkdownText
        $this->exportarRelacionJSON($ruta, $curso, 'markdown_text');

        // YoutubeVideo
        $this->exportarFicheroJSON($ruta, 'youtube_videos.json', $curso->youtube_videos);

        // Actividad "*" -- "*" YoutubeVideo
        $this->exportarRelacionJSON($ruta, $curso, 'youtube_video');

        // FileResources
        $this->exportarFicheroJSON($ruta, 'file_resources.json', $curso->file_resources);

        // Files
        $this->exportarFicheroJSON($ruta, 'file_resources_files.json', $curso->file_resources_files->sortBy('orden'));
        $this->exportarFileResources($directorio, $curso);

        // Actividad "*" -- "*" FileResources
        $this->exportarRelacionJSON($ruta, $curso, 'file_resource');

        // LinkCollections
        $this->exportarFicheroJSON($ruta, 'link_collections.json', $curso->link_collections);

        // Links
        $this->exportarFicheroJSON($ruta, 'link_collections_links.json', $curso->link_collections_links->sortBy('orden'));

        // Actividad "*" -- "*" LinkCollections
        $this->exportarRelacionJSON($ruta, $curso, 'link_collection');

        // Cuestionario
        $cuestionarios = $curso->cuestionarios()->plantilla()->get();
        $this->exportarFicheroJSON($ruta, 'cuestionarios.json', $cuestionarios);

        // Pregunta
        $this->exportarFicheroJSON($ruta, 'preguntas.json', $curso->preguntas()->plantilla()->orderBy('orden')->get());

        // Item
        $this->exportarFicheroJSON($ruta, 'items.json', $curso->items()->plantilla()->orderBy('orden')->get());

        // Actividad "*" -- "*" Cuestionario
        $this->exportarRelacionJSON($ruta, $curso, 'cuestionario');

        // FileUpload
        $file_uploads = $curso->file_uploads()->plantilla()->get();
        $this->exportarFicheroJSON($ruta, 'file_uploads.json', $file_uploads);

        // Actividad "*" -- "*" FileUpload
        $this->exportarRelacionJSON($ruta, $curso, 'file_upload');

        // Feedback
        $this->exportarFicheroJSON($ruta, 'feedbacks_curso.json', $curso->feedbacks()->orderBy('orden')->get());

        // Milestone
        $this->exportarFicheroJSON($ruta, 'milestones.json', $curso->milestones()->orderBy('date')->get());

        // Rubric
        $rubrics = $curso->rubrics()->plantilla()->get();
        $this->exportarFicheroJSON($ruta, 'rubrics.json', $rubrics);

        // CriteriaGroup
        $this->exportarFicheroJSON($ruta, 'criteria_groups.json', $curso->criteria_groups()->plantilla()->orderBy('orden')->get());

        // Criteria
        $this->exportarFicheroJSON($ruta, 'criterias.json', $curso->criterias()->plantilla()->orderBy('orden')->get());

        // Actividad "*" -- "*" Rubric
        $this->exportarRelacionJSON($ruta, $curso, 'rubric');

        // TestResult
        $test_results = $curso->test_results()->plantilla()->get();
        $this->exportarFicheroJSON($ruta, 'test_results.json', $test_results);

        // Actividad "*" -- "*" TestResult
        $this->exportarRelacionJSON($ruta, $curso, 'test_result');

        $datos = new Collection();
        foreach ($curso->actividades()->plantilla()->get() as $actividad) {
            foreach ($actividad->feedbacks()->orderBy('orden')->get() as $feedback) {
                $datos->add($feedback);
            }
        }
        $this->exportarFicheroJSON($ruta, 'feedbacks_actividades.json', $datos);

        // SafeExam
        if (isset($curso->safe_exam)) {
            $this->exportarFicheroJSON($ruta, 'safe_exam.json', $curso->safe_exam);
            $this->exportarFicheroJSON($ruta, 'safe_exam_allowed_apps.json', $curso->safe_exam->allowed_apps);
            $this->exportarFicheroJSON($ruta, 'safe_exam_allowed_urls.json', $curso->safe_exam->allowed_urls);
        }

        // Selector
        $this->exportarFicheroJSON($ruta, 'selectors.json', $curso->selectors);
        $this->exportarFicheroJSON($ruta, 'selectors_rule_groups.json', $curso->rule_groups);
        $this->exportarFicheroJSON($ruta, 'selectors_rules.json', $curso->rules);

        // Actividad "*" -- "*" Selector
        $this->exportarRelacionJSON($ruta, $curso, 'selector');

        // Exportar el log
        $this->log_txt = Arr::sort($this->log_txt);
        $this->log_txt = Arr::join($this->log_txt, PHP_EOL);
        SystemFile::append($ruta . "log.txt", $this->log_txt);

        // Crear el zip
        $fecha = now()->format('Ymd-His');
        $nombre = Str::slug($curso->full_name);

        dispatch(function () use ($directorio) {
            Log::debug('Borrando...', [
                'directorio' => $directorio,
            ]);
            Storage::disk('temp')->deleteDirectory($directorio);
        })->afterResponse();

        return $this->zipDirectoryWithSubdirs("ikasgela-{$nombre}-{$fecha}.zip", $directorio);
    }

    private $log_txt = [];

    private function exportarFicheroJSON(string $ruta, string $fichero, $datos): void
    {
        $ok = SystemFile::put($ruta . $fichero, $datos->toJson(JSON_PRETTY_PRINT));
        if ($ok) {
            $this->log_txt[] = $fichero . ': ' . (!is_a($datos, Curso::class) ? $datos->count() : 1);
        } else {
            $this->log_txt[] = $fichero . ': ERROR';
        }
    }

    public function import(StoreFile $request)
    {
        $this->validate($request, [
            'file' => 'required',
            'category_id' => 'required',
        ]);

        // Crear el directorio temporal
        $directorio = '/' . Str::uuid() . '/';
        Storage::disk('temp')->makeDirectory($directorio);

        $ruta = Storage::disk('temp')->path($directorio);

        $fichero = $request->file;
        $filename = $directorio . '/' . $fichero->getClientOriginalName();
        $filename_full = Storage::disk('temp')->path($filename);

        Storage::disk('temp')->put($filename, file_get_contents($fichero));

        // Descomprimir el archivo zip
        $zip = new ZipArchive();
        if ($zip->open($filename_full)) {
            $zip->extractTo($ruta);
            $zip->close();
        }

        ImportCurso::dispatch(request('category_id'), $directorio, $ruta);

        return redirect()->route('cursos.index');
    }

    private function exportarRelacionJSON(string $ruta, $curso, $tabla): void
    {
        $datos = DB::table("actividad_{$tabla}")
            ->join('actividades', "actividad_{$tabla}.actividad_id", '=', 'actividades.id')
            ->where('actividades.plantilla', '=', true)
            ->join($tabla . 's', "actividad_{$tabla}.{$tabla}_id", '=', $tabla . 's.id')
            ->where($tabla . 's.curso_id', '=', $curso->id)
            ->select("actividad_{$tabla}.*")
            ->orderBy('orden')
            ->get();
        $this->exportarFicheroJSON($ruta, "actividad_{$tabla}.json", $datos);
    }

    public function matricular(Curso $curso, User $user)
    {
        $curso->users()->attach($user);

        $user->addEtiqueta($curso->tags);
        $user->save();

        setting_usuario(['curso_actual' => $curso->id]);
        $user->clearCache();
        $user->clearSession();

        $this->asignarTareaBienvenida($curso, $user);

        return back();
    }

    public function curso_actual(Curso $curso, User $user)
    {
        setting_usuario(['curso_actual' => $curso->id]);
        $user->clearCache();
        $user->clearSession();

        return back();
    }

    public function reset(Curso $curso)
    {
        foreach ($curso->hilos()->get() as $hilo) {
            DB::table('messages')
                ->where('thread_id', '=', $hilo->id)
                ->delete();

            DB::table('participants')
                ->where('thread_id', '=', $hilo->id)
                ->delete();
        }

        $curso->hilos()->delete();

        return back();
    }

    public function limpiar_cache(Curso $curso)
    {
        foreach ($curso->users as $user) {
            $user->clearCache();
        }

        return back();
    }

    private function exportarRepositorios(string $ruta, Curso $curso)
    {
        $path = $ruta . '/repositorios/';

        foreach ($curso->intellij_projects as $intellij_project) {
            $repositorio = $intellij_project->repository_no_cache();
            $this->clonarRepositorio($path, $repositorio);
        }
    }

    private function exportarMarkdown(string $ruta, Curso $curso)
    {
        $path = $ruta . '/markdown/';

        foreach ($curso->markdown_texts as $markdown_text) {
            $repositorio = GiteaClient::repo($markdown_text->repositorio);
            $this->clonarRepositorio($path, $repositorio);
        }
    }

    private function exportarFileResources(string $directorio, Curso $curso)
    {
        $ruta = $directorio . '/file_resources/';
        Storage::disk('temp')->makeDirectory($ruta);

        foreach ($curso->file_resources as $file_resource) {
            foreach ($file_resource->files as $file) {
                $origen = 'documents/' . $file->path;
                $destino = $ruta . '/' . $file->path;
                try {
                    $datos = Storage::disk('s3')->get($origen);
                    Storage::disk('temp')->put($destino, $datos);
                } catch (UnableToReadFile) {
                    Log::error("Error al exportar un fichero de S3", [
                        'origen' => $origen,
                    ]);
                }
            }
        }
    }

    private function clonarRepositorio(string $path, array $repositorio): void
    {
        $response = Process::path($path)
            ->run('git clone http://root:' . config('gitea.token') . '@gitea:3000/'
                . $repositorio['path_with_namespace'] . '.git '
                . $repositorio['owner'] . '@' . $repositorio['name']);

        if (!$response->successful()) {
            Log::error('Error al descargar repositorios mediante Git.', [
                'output' => $response->errorOutput()
            ]);
        } else {
            Process::path($path . '/' . $repositorio['owner'] . '@' . $repositorio['name'])
                ->run('git remote remove origin');
        }
    }

    public function zipDirectoryWithSubdirs(string $zip, string $directory)
    {
        $zip_path = Storage::disk('temp')->path($zip);

        $zip = new ZipArchive();

        if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {

            $files = Storage::disk('temp')->allFiles($directory);
            foreach ($files as $file) {
                $filePath = Storage::disk('temp')->path($file);
                $relative_file = Str::replaceStart($directory, '', '/' . $file);
                $zip->addFile($filePath, $relative_file);
            }

            $zip->close();

            return response()->download($zip_path)->deleteFileAfterSend(true);
        } else {
            return "Failed to create the zip file.";
        }
    }

    public function toggle_matricula_abierta(Curso $curso)
    {
        $curso->matricula_abierta = !$curso->matricula_abierta;
        $curso->save();

        return back();
    }
}
