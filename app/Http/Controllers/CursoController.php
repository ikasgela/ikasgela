<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Category;
use App\Cuestionario;
use App\Curso;
use App\FileResource;
use App\FileUpload;
use App\IntellijProject;
use App\Item;
use App\MarkdownText;
use App\Pregunta;
use App\Qualification;
use App\Skill;
use App\Unidad;
use App\YoutubeVideo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CursoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
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
            'qualification_id' => request('qualification_id'),
            'max_simultaneas' => request('max_simultaneas'),
            'plazo_actividad' => request('plazo_actividad'),
            'fecha_inicio' => request('fecha_inicio'),
            'fecha_fin' => request('fecha_fin'),
            'minimo_entregadas' => request('minimo_entregadas'),
            'minimo_competencias' => request('minimo_competencias'),
            'minimo_examenes' => request('minimo_examenes'),
            'examenes_obligatorios' => $request->has('examenes_obligatorios'),
            'maximo_recuperable_examenes_finales' => request('maximo_recuperable_examenes_finales'),
        ]);

        return retornar();
    }

    public function show(Curso $curso)
    {
        return abort(501);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Curso $curso
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
            'qualification_id' => request('qualification_id'),
            'max_simultaneas' => request('max_simultaneas'),
            'plazo_actividad' => request('plazo_actividad'),
            'fecha_inicio' => request('fecha_inicio'),
            'fecha_fin' => request('fecha_fin'),
            'minimo_entregadas' => request('minimo_entregadas'),
            'minimo_competencias' => request('minimo_competencias'),
            'minimo_examenes' => request('minimo_examenes'),
            'examenes_obligatorios' => $request->has('examenes_obligatorios'),
            'maximo_recuperable_examenes_finales' => request('maximo_recuperable_examenes_finales'),
        ]);

        return retornar();
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();

        return back();
    }

    public function export()
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        $this->exportarFicheroJSON('curso.json', $curso_actual);
        $this->exportarFicheroJSON('qualifications.json', $curso_actual->qualifications);
        $this->exportarFicheroJSON('skills.json', $curso_actual->skills);
        $this->exportarFicheroJSON('qualification_skill.json', DB::table('qualification_skill')->get());
        $this->exportarFicheroJSON('unidades.json', $curso_actual->unidades);

        // Actividad
        $this->exportarFicheroJSON('actividades.json', $curso_actual->actividades()->plantilla()->get());

        // IntellijProject
        $this->exportarFicheroJSON('intellij_projects.json', $curso_actual->intellij_projects);

        // Actividad "*" -- "*" IntellijProject
        $this->exportarRelacionJSON('intellij_project');

        // MarkdownText
        $this->exportarFicheroJSON('markdown_texts.json', $curso_actual->markdown_texts);

        // Actividad "*" -- "*" MarkdownText
        $this->exportarRelacionJSON('markdown_text');

        // YoutubeVideo
        $this->exportarFicheroJSON('youtube_videos.json', $curso_actual->youtube_videos);

        // Actividad "*" -- "*" YoutubeVideo
        $this->exportarRelacionJSON('youtube_video');

        // FileResources
        $this->exportarFicheroJSON('file_resources.json', $curso_actual->file_resources);

        // Actividad "*" -- "*" FileResources
        $this->exportarRelacionJSON('file_resource');

        // Cuestionario
        $cuestionarios = $curso_actual->cuestionarios()->plantilla()->get();
        $this->exportarFicheroJSON('cuestionarios.json', $cuestionarios);

        // Pregunta
        $this->exportarFicheroJSON('preguntas.json', $curso_actual->preguntas()->plantilla()->get());

        // Item
        $this->exportarFicheroJSON('items.json', $curso_actual->items()->plantilla()->get());

        // Actividad "*" -- "*" Cuestionario
        $this->exportarRelacionJSON('cuestionario');

        // FileUpload
        $file_uploads = FileUpload::where('curso_id', $curso_actual->id)->plantilla()->get();
        $this->exportarFicheroJSON('file_uploads.json', $file_uploads);

        // Actividad "*" -- "*" FileUpload
        $this->exportarRelacionJSON('file_upload');

        return back();
    }

    private function exportarFicheroJSON(string $fichero, $datos): void
    {
        File::put(storage_path('/temp/' . $fichero), $datos->toJson(JSON_PRETTY_PRINT));
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

    public function import()
    {
        $import_ids = [
            'cursos', 'qualifications', 'skills', 'unidades', 'actividades',
            'intellij_projects', 'markdown_texts', 'youtube_videos', 'file_resources',
            'file_uploads', 'cuestionarios', 'preguntas', 'items'
        ];

        foreach ($import_ids as $import_id) {
            $this->addImportId($import_id);
        }

        // Curso
        $json = $this->cargarFichero('/temp/curso.json');
        $json['nombre'] .= '-' . bin2hex(openssl_random_pseudo_bytes(3));
        $json['slug'] = Str::slug($json['nombre']);

        // Curso -- Qualification
        $temp_curso_qualification_id = $json['qualification_id'];

        // Curso
        $curso = Curso::create(array_merge($json, [
            'qualification_id' => null,
        ]));

        // Curso -- "*" Qualification
        $json = $this->cargarFichero('/temp/qualifications.json');
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
        $json = $this->cargarFichero('/temp/skills.json');
        foreach ($json as $objeto) {
            Skill::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Qualification "*" -- "*" Skill
        $json = $this->cargarFichero('/temp/qualification_skill.json');
        foreach ($json as $objeto) {
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            $skill = Skill::where('__import_id', $objeto['skill_id'])->first();
            $qualification?->skills()->attach($skill, ['percentage' => $objeto['percentage']]);
        }

        // Curso -- "*" Unidad
        // Unidad -- Qualification
        $json = $this->cargarFichero('/temp/unidades.json');
        foreach ($json as $objeto) {
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            Unidad::create(array_merge($objeto, [
                'curso_id' => $curso->id,
                'qualification_id' => $qualification?->id,
            ]));
        }

        // Unidad -- "*" Actividad
        // Unidad -- Qualification

        Schema::disableForeignKeyConstraints();

        $json = $this->cargarFichero('/temp/actividades.json');
        foreach ($json as $objeto) {
            $unidad = !is_null($objeto['unidad_id']) ? Unidad::where('__import_id', $objeto['unidad_id'])->first() : null;
            $qualification = !is_null($objeto['qualification_id']) ? Qualification::where('__import_id', $objeto['qualification_id'])->first() : null;
            Actividad::create(array_merge($objeto, [
                'unidad_id' => $unidad->id,
                'qualification_id' => $qualification?->id,
            ]));
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
        $json = $this->cargarFichero('/temp/intellij_projects.json');
        foreach ($json as $objeto) {
            IntellijProject::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" IntellijProject
        $json = $this->cargarFichero('/temp/actividad_intellij_project.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $intellij_project = !is_null($objeto['intellij_project_id']) ? IntellijProject::where('__import_id', $objeto['intellij_project_id'])->first() : null;
            $actividad?->intellij_projects()->attach($intellij_project);
        }

        // Curso -- "*" MarkdownText
        $json = $this->cargarFichero('/temp/markdown_texts.json');
        foreach ($json as $objeto) {
            MarkdownText::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" MarkdownText
        $json = $this->cargarFichero('/temp/actividad_markdown_text.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $markdown_text = !is_null($objeto['markdown_text_id']) ? MarkdownText::where('__import_id', $objeto['markdown_text_id'])->first() : null;
            $actividad?->markdown_texts()->attach($markdown_text);
        }

        // Curso -- "*" YoutubeVideo
        $json = $this->cargarFichero('/temp/youtube_videos.json');
        foreach ($json as $objeto) {
            YoutubeVideo::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" YoutubeVideo
        $json = $this->cargarFichero('/temp/actividad_youtube_video.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $youtube_video = !is_null($objeto['youtube_video_id']) ? YoutubeVideo::where('__import_id', $objeto['youtube_video_id'])->first() : null;
            $actividad?->youtube_videos()->attach($youtube_video);
        }

        // Curso -- "*" FileResource
        $json = $this->cargarFichero('/temp/file_resources.json');
        foreach ($json as $objeto) {
            FileResource::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" FileResource
        $json = $this->cargarFichero('/temp/actividad_file_resource.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $file_resource = !is_null($objeto['file_resource_id']) ? FileResource::where('__import_id', $objeto['file_resource_id'])->first() : null;
            $actividad?->file_resources()->attach($file_resource);
        }

        // Curso -- "*" FileUpload
        $json = $this->cargarFichero('/temp/file_uploads.json');
        foreach ($json as $objeto) {
            FileUpload::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Actividad "*" - "*" FileUpload
        $json = $this->cargarFichero('/temp/actividad_file_upload.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $file_upload = !is_null($objeto['file_upload_id']) ? FileUpload::where('__import_id', $objeto['file_upload_id'])->first() : null;
            $actividad?->file_uploads()->attach($file_upload);
        }

        // Curso -- "*" Cuestionario
        $json = $this->cargarFichero('/temp/cuestionarios.json');
        foreach ($json as $objeto) {
            Cuestionario::create(array_merge($objeto, [
                'curso_id' => $curso->id,
            ]));
        }

        // Cuestionario -- "*" Pregunta
        $json = $this->cargarFichero('/temp/preguntas.json');
        foreach ($json as $objeto) {
            $cuestionario = !is_null($objeto['cuestionario_id']) ? Cuestionario::where('__import_id', $objeto['cuestionario_id'])->first() : null;
            Pregunta::create(array_merge($objeto, [
                'cuestionario_id' => $cuestionario?->id,
            ]));
        }

        // Pregunta -- "*" Item
        $json = $this->cargarFichero('/temp/items.json');
        foreach ($json as $objeto) {
            $pregunta = !is_null($objeto['pregunta_id']) ? Pregunta::where('__import_id', $objeto['pregunta_id'])->first() : null;
            Item::create(array_merge($objeto, [
                'pregunta_id' => $pregunta?->id,
            ]));
        }

        // Actividad "*" - "*" Cuestionario
        $json = $this->cargarFichero('/temp/actividad_cuestionario.json');
        foreach ($json as $objeto) {
            $actividad = !is_null($objeto['actividad_id']) ? Actividad::where('__import_id', $objeto['actividad_id'])->first() : null;
            $cuestionario = !is_null($objeto['cuestionario_id']) ? Cuestionario::where('__import_id', $objeto['cuestionario_id'])->first() : null;
            $actividad?->cuestionarios()->attach($cuestionario);
        }

        foreach ($import_ids as $import_id) {
            $this->removeImportId($import_id);
        }

        return back();
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

    private function cargarFichero($fichero): array
    {
        // Cargar el fichero
        $path = storage_path() . $fichero;
        $json = json_decode(file_get_contents($path), true);
        $json = $this->replaceKeys('id', '__import_id', $json);
        $this->removeKey($json, 'created_at');
        $this->removeKey($json, 'updated_at');
        $this->removeKey($json, 'deleted_at');
        return $json;
    }

    private function exportarRelacionJSON($tabla): void
    {
        $datos = DB::table("actividad_{$tabla}")
            ->join('actividades', "actividad_{$tabla}.actividad_id", '=', 'actividades.id')
            ->where('actividades.plantilla', '=', true)
            ->select("actividad_{$tabla}.*")
            ->get();
        $this->exportarFicheroJSON("actividad_{$tabla}.json", $datos);
    }
}
