<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFile;
use App\Models\Actividad;
use App\Models\Category;
use App\Models\Cuestionario;
use App\Models\Curso;
use App\Models\Feedback;
use App\Models\File;
use App\Models\FileResource;
use App\Models\FileUpload;
use App\Models\IntellijProject;
use App\Models\Item;
use App\Models\MarkdownText;
use App\Models\Milestone;
use App\Models\Pregunta;
use App\Models\Qualification;
use App\Models\Skill;
use App\Models\Unidad;
use App\Models\User;
use App\Models\YoutubeVideo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as SystemFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;
use Zip;
use ZipArchive;

class CursoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['matricular', 'curso_actual']);;
    }

    public function index()
    {
        $cursos = Curso::all();

        return view('cursos.index', compact('cursos'));
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
        ]);

        Curso::create([
            'category_id' => request('category_id'),
            'nombre' => request('nombre'),
            'descripcion' => request('descripcion'),
            'slug' => Str::slug(request('nombre')),
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
        ]);

        return retornar();
    }

    public function show(Curso $curso)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Curso $curso
     * @return \Illuminate\Http\Response
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
        ]);

        $curso->update([
            'category_id' => request('category_id'),
            'nombre' => request('nombre'),
            'descripcion' => request('descripcion'),
            'slug' => strlen(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('nombre')),
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
        ]);

        return retornar();
    }

    public function destroy(Curso $curso)
    {
        foreach ($curso->actividades()->get() as $actividad) {
            DB::table('tareas')
                ->where('actividad_id', '=', $actividad->id)
                ->delete();

            DB::table('actividad_team')
                ->where('actividad_id', '=', $actividad->id)
                ->delete();

            $actividad->feedbacks()->delete();
        }

        DB::table('curso_user')
            ->where('curso_id', '=', $curso->id)
            ->delete();

        $curso->intellij_projects()->forceDelete();
        $curso->markdown_texts()->forceDelete();
        $curso->youtube_videos()->forceDelete();
        $curso->cuestionarios()->forceDelete();

        $curso->file_resources_files()->forceDelete();
        $curso->file_resources()->forceDelete();
        $curso->file_uploads_files()->forceDelete();
        $curso->file_uploads()->forceDelete();

        Schema::disableForeignKeyConstraints();
        $curso->actividades()->forceDelete();
        Schema::enableForeignKeyConstraints();

        $curso->unidades()->forceDelete();

        Schema::disableForeignKeyConstraints();
        foreach ($curso->qualifications()->get() as $qualification) {
            $qualification->skills()->sync([]);
        }
        $curso->qualifications()->forceDelete();
        $curso->skills()->forceDelete();
        Schema::enableForeignKeyConstraints();

        $curso->feedbacks()->delete();

        $curso->milestones()->delete();

        foreach ($curso->hilos()->get() as $hilo) {
            DB::table('messages')
                ->where('thread_id', '=', $hilo->id)
                ->delete();

            DB::table('participants')
                ->where('thread_id', '=', $hilo->id)
                ->delete();
        }

        $curso->hilos()->forceDelete();

        Schema::disableForeignKeyConstraints();
        $curso->forceDelete();
        Schema::enableForeignKeyConstraints();

        return back();
    }

    public function export(Curso $curso)
    {
        // Crear el directorio temporal
        $directorio = '/' . Str::uuid() . '/';
        Storage::disk('temp')->makeDirectory($directorio);
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

        // Actividad "*" -- "*" IntellijProject
        $this->exportarRelacionJSON($ruta, $curso, 'intellij_project');

        // MarkdownText
        $this->exportarFicheroJSON($ruta, 'markdown_texts.json', $curso->markdown_texts);

        // Actividad "*" -- "*" MarkdownText
        $this->exportarRelacionJSON($ruta, $curso, 'markdown_text');

        // YoutubeVideo
        $this->exportarFicheroJSON($ruta, 'youtube_videos.json', $curso->youtube_videos);

        // Actividad "*" -- "*" YoutubeVideo
        $this->exportarRelacionJSON($ruta, $curso, 'youtube_video');

        // FileResources
        $this->exportarFicheroJSON($ruta, 'file_resources.json', $curso->file_resources);

        // Files
        $this->exportarFicheroJSON($ruta, 'file_resources_files.json', $curso->file_resources_files);

        // Actividad "*" -- "*" FileResources
        $this->exportarRelacionJSON($ruta, $curso, 'file_resource');

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

        $datos = new Collection();
        foreach ($curso->actividades()->get() as $actividad) {
            foreach ($actividad->feedbacks()->orderBy('orden')->get() as $feedback) {
                $datos->add($feedback);
            }
        }
        $this->exportarFicheroJSON($ruta, 'feedbacks_actividades.json', $datos);

        // Crear el zip
        $fecha = now()->format('Ymd-His');
        $nombre = Str::slug($curso->full_name);

        $ficheros = Storage::disk('temp')->files($directorio);

        $ficheros_ruta_completa = [];
        foreach ($ficheros as $fichero) {
            array_push($ficheros_ruta_completa, Storage::disk('temp')->path($fichero));
        }

        // Almacenar el directorio para borrarlo al terminar con un evento
        //session(['_delete_me' => $directorio]);

        dispatch(function () use ($directorio) {
            Log::debug('Borrando...', [
                'directorio' => $directorio,
            ]);
            Storage::disk('temp')->deleteDirectory($directorio);
        })->afterResponse();

        return Zip::create("ikasgela-{$nombre}-{$fecha}.zip", $ficheros_ruta_completa);
    }

    private function exportarFicheroJSON(string $ruta, string $fichero, $datos): void
    {
        SystemFile::put($ruta . $fichero, $datos->toJson(JSON_PRETTY_PRINT));
    }

    function replaceKeys($oldKey, $newKey, array $input)
    {
        $return = array();
        foreach ($input as $key => $value) {
            if ($key === $oldKey)
                $key = $newKey;

            if (is_array($value))
                $value = $this->replaceKeys($oldKey, $newKey, $value);

            $return[$key] = $value;
        }
        return $return;
    }

    // REF: https://stackoverflow.com/a/1708914
    function removeKey(&$array, $unwanted_key)
    {
        unset($array[$unwanted_key]);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->removeKey($value, $unwanted_key);
            }
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

        // Importar los datos desde los archivos JSON
        $import_ids = [
            'cursos', 'unidades', 'actividades',
            'qualifications', 'skills',
            'intellij_projects', 'markdown_texts', 'youtube_videos',
            'file_resources', 'files',
            'file_uploads',
            'cuestionarios', 'preguntas', 'items',
            'feedback',
            'milestones',
        ];

        foreach ($import_ids as $import_id) {
            $this->addImportId($import_id);
        }

        // Curso
        $json = $this->cargarFichero($ruta, 'curso.json');
        $json['nombre'] .= '-' . bin2hex(openssl_random_pseudo_bytes(3));
        $json['slug'] = Str::slug($json['nombre']);

        // Curso -- Qualification
        $temp_curso_qualification_id = $json['qualification_id'];

        // Curso
        $curso = Curso::create(array_merge($json, [
            'qualification_id' => null,
            'category_id' => request('category_id'),
        ]));

        // Curso -- "*" Qualification
        $json = $this->cargarFichero($ruta, 'qualifications.json');
        foreach ($json as $objeto) {
            Qualification::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Curso -- Qualification
        $qualification = !is_null($temp_curso_qualification_id) ? Qualification::where('__import_id', $temp_curso_qualification_id)->first() : null;
        $curso->qualification_id = $qualification?->id;
        $curso->save();

        // Curso -- "*" Skill
        $json = $this->cargarFichero($ruta, 'skills.json');
        foreach ($json as $objeto) {
            Skill::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Qualification "*" -- "*" Skill
        $json = $this->cargarFichero($ruta, 'qualification_skill.json');
        foreach ($json as $objeto) {
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            $skill = Skill::where('__import_id', $objeto['skill_id'])->first();
            $qualification?->skills()->attach($skill, [
                'percentage' => $objeto['percentage'],
                'orden' => Str::orderedUuid(),
            ]);
        }

        // Curso -- "*" Unidad
        // Unidad -- Qualification
        $json = $this->cargarFichero($ruta, 'unidades.json');
        foreach ($json as $objeto) {
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            $unidad = Unidad::create(array_merge($objeto, [
                'curso_id' => $curso->id,
                'qualification_id' => $qualification?->id,
            ]));
            $unidad->orden = $unidad->id;
            $unidad->save();
        }

        // Unidad -- "*" Actividad
        // Unidad -- Qualification

        Schema::disableForeignKeyConstraints();

        $json = $this->cargarFichero($ruta, 'actividades.json');
        foreach ($json as $objeto) {
            $unidad = !is_null($objeto['unidad_id']) ? Unidad::where('__import_id', $objeto['unidad_id'])->first() : null;
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            $actividad = Actividad::create(array_merge($objeto, [
                'unidad_id' => $unidad->id,
                'qualification_id' => $qualification?->id,
            ]));
            $actividad->orden = $actividad->id;
            $actividad->save();
        }

        // Actividad --> Actividad: siguiente
        $actividades = Actividad::whereNotNull('__import_id')->get();
        foreach ($actividades as $actividad) {
            $siguiente = !is_null($actividad->siguiente_id) ? Actividad::where('__import_id', $actividad->siguiente_id)->first() : null;
            $actividad->siguiente_id = $siguiente?->id;
            $actividad->save();
        }

        Schema::enableForeignKeyConstraints();

        // Curso -- "*" IntellijProject
        $json = $this->cargarFichero($ruta, 'intellij_projects.json');
        foreach ($json as $objeto) {
            IntellijProject::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" IntellijProject
        $json = $this->cargarFichero($ruta, 'actividad_intellij_project.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $intellij_project = !is_null($objeto['intellij_project_id']) ? IntellijProject::where('__import_id', $objeto['intellij_project_id'])->first() : null;
            $actividad?->intellij_projects()->attach($intellij_project, ['orden' => Str::orderedUuid()]);
        }

        // Curso -- "*" MarkdownText
        $json = $this->cargarFichero($ruta, 'markdown_texts.json');
        foreach ($json as $objeto) {
            MarkdownText::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" MarkdownText
        $json = $this->cargarFichero($ruta, 'actividad_markdown_text.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $markdown_text = !is_null($objeto['markdown_text_id']) ? MarkdownText::where('__import_id', $objeto['markdown_text_id'])->first() : null;
            $actividad?->markdown_texts()->attach($markdown_text, ['orden' => Str::orderedUuid()]);
        }

        // Curso -- "*" YoutubeVideo
        $json = $this->cargarFichero($ruta, 'youtube_videos.json');
        foreach ($json as $objeto) {
            YoutubeVideo::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" YoutubeVideo
        $json = $this->cargarFichero($ruta, 'actividad_youtube_video.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $youtube_video = !is_null($objeto['youtube_video_id']) ? YoutubeVideo::where('__import_id', $objeto['youtube_video_id'])->first() : null;
            $actividad?->youtube_videos()->attach($youtube_video, ['orden' => Str::orderedUuid()]);
        }

        // Curso -- "*" FileResource
        $json = $this->cargarFichero($ruta, 'file_resources.json');
        foreach ($json as $objeto) {
            FileResource::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" FileResource
        $json = $this->cargarFichero($ruta, 'actividad_file_resource.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $file_resource = !is_null($objeto['file_resource_id']) ? FileResource::where('__import_id', $objeto['file_resource_id'])->first() : null;
            $actividad?->file_resources()->attach($file_resource, ['orden' => Str::orderedUuid()]);
        }

        // FileResource -- "*" File
        $json = $this->cargarFichero($ruta, 'file_resources_files.json');
        foreach ($json as $objeto) {
            $file_resource = !is_null($objeto['uploadable_id']) ? FileResource::where('__import_id', $objeto['uploadable_id'])->first() : null;
            File::create(array_merge($objeto, [
                'uploadable_id' => $file_resource->id,
                'user_id' => null,
            ]));
        }

        // Curso -- "*" FileUpload
        $json = $this->cargarFichero($ruta, 'file_uploads.json');
        foreach ($json as $objeto) {
            FileUpload::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" FileUpload
        $json = $this->cargarFichero($ruta, 'actividad_file_upload.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $file_upload = !is_null($objeto['file_upload_id']) ? FileUpload::where('__import_id', $objeto['file_upload_id'])->first() : null;
            $actividad?->file_uploads()->attach($file_upload, ['orden' => Str::orderedUuid()]);
        }

        // Curso -- "*" Cuestionario
        $json = $this->cargarFichero($ruta, 'cuestionarios.json');
        foreach ($json as $objeto) {
            Cuestionario::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Cuestionario -- "*" Pregunta
        $json = $this->cargarFichero($ruta, 'preguntas.json');
        foreach ($json as $objeto) {
            $cuestionario = !is_null($objeto['cuestionario_id']) ? Cuestionario::where('__import_id', $objeto['cuestionario_id'])->first() : null;
            $pregunta = Pregunta::create(array_merge($objeto, [
                'cuestionario_id' => $cuestionario?->id,
            ]));
            $pregunta->orden = $pregunta->id;
            $pregunta->save();
        }

        // Pregunta -- "*" Item
        $json = $this->cargarFichero($ruta, 'items.json');
        foreach ($json as $objeto) {
            $pregunta = !is_null($objeto['pregunta_id']) ? Pregunta::where('__import_id', $objeto['pregunta_id'])->first() : null;
            $item = Item::create(array_merge($objeto, [
                'pregunta_id' => $pregunta?->id,
            ]));
            $item->orden = $item->id;
            $item->save();
        }

        // Actividad "*" - "*" Cuestionario
        $json = $this->cargarFichero($ruta, 'actividad_cuestionario.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $cuestionario = !is_null($objeto['cuestionario_id']) ? Cuestionario::where('__import_id', $objeto['cuestionario_id'])->first() : null;
            $actividad?->cuestionarios()->attach($cuestionario, ['orden' => Str::orderedUuid()]);
        }

        // Curso -- "*" Feedback
        $json = $this->cargarFichero($ruta, 'feedbacks_curso.json');
        foreach ($json as $objeto) {
            $feedback = Feedback::create(array_merge($objeto, [
                'comentable_id' => $curso->id,
                'comentable_type' => Curso::class,
            ]));
            $feedback->orden = $feedback->id;
            $feedback->save();
        }

        // Actividad -- "*" Feedback
        $json = $this->cargarFichero($ruta, 'feedbacks_actividades.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['comentable_id']) ? Actividad::where('__import_id', $objeto['comentable_id'])->first() : null;
            if (!is_null($actividad)) {
                $feedback = Feedback::create(array_merge($objeto, [
                    'comentable_id' => $actividad->id,
                    'comentable_type' => Actividad::class,
                ]));
                $feedback->orden = $feedback->id;
                $feedback->save();
            }
        }

        // Curso -- "*" Milestone
        $json = $this->cargarFichero($ruta, 'milestones.json');
        foreach ($json as $objeto) {
            Milestone::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        foreach ($import_ids as $import_id) {
            $this->removeImportId($import_id);
        }

        // Borrar el directorio temporal
        Storage::disk('temp')->deleteDirectory($directorio);

        return redirect()->route('cursos.index');
    }

    private function addImportId($tabla): void
    {
        // Añadir la columna __import_id
        if (!Schema::hasColumn($tabla, '__import_id')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->bigInteger('__import_id')->unsigned()->nullable();
            });
        }
    }

    private function removeImportId($tabla): void
    {
        // Quitar la columna
        if (Schema::hasColumn($tabla, '__import_id')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropColumn('__import_id');
            });
        }
    }

    private function cargarFichero(string $ruta, $fichero): array
    {
        // Cargar el fichero
        $path = $ruta . '/' . $fichero;
        $json = json_decode(file_get_contents($path), true);
        $json = $this->replaceKeys('id', '__import_id', $json);
        $this->removeKey($json, 'created_at');
        $this->removeKey($json, 'updated_at');
        $this->removeKey($json, 'deleted_at');
        return $json;
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

        setting_usuario(['curso_actual' => $curso->id]);
        $user->clearCache();

        // Asignar la tarea de bienvenida
        $actividad = Actividad::whereHas('unidad.curso', function ($query) use ($curso) {
            $query->where('id', $curso->id);
        })->where('slug', 'tarea-de-bienvenida')
            ->where('plantilla', true)
            ->first();

        if (isset($actividad)) {
            $clon = $actividad->duplicate();
            $clon->plantilla_id = $actividad->id;
            $clon->save();
            $user->actividades()->attach($clon, ['puntuacion' => $actividad->puntuacion]);
        }

        return back();
    }

    public function curso_actual(Curso $curso, User $user)
    {
        setting_usuario(['curso_actual' => $curso->id]);
        $user->clearCache();

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

        $curso->hilos()->forceDelete();

        return back();
    }
}
