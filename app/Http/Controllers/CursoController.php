<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Category;
use App\Curso;
use App\FileResource;
use App\IntellijProject;
use App\MarkdownText;
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

        $actividades = Actividad::whereHas('unidad.curso', function ($query) use ($curso_actual) {
            $query->where('curso_id', $curso_actual->id);
        })->plantilla()->get();

        $this->exportarFicheroJSON('actividades.json', $actividades);

        $recursos = [
            'intellij_projects',
            'youtube_videos',
            'markdown_texts',
            'cuestionarios',
            'file_uploads',
            'file_resources',
        ];

        foreach ($recursos as $recurso) {
            $this->exportarFicheroJSON($recurso . '.json', $curso_actual->$recurso);
        }

        $asociaciones = [
            'actividad_intellij_project',
            'actividad_markdown_text',
            'actividad_cuestionario',
            'actividad_file_resource',
            'actividad_file_upload',
            'actividad_youtube_video',
        ];

        foreach ($asociaciones as $asociacion) {
            $this->exportarFicheroJSON($asociacion . '.json', DB::table($asociacion)->get());
        }

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
            'file_uploads', 'cuestionarios'
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
}
