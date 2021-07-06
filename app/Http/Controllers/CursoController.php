<?php

namespace App\Http\Controllers;

use App\Category;
use App\Curso;
use App\Qualification;
use App\Skill;
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
        $qualifications = Qualification::cursoActual()->orderBy('name')->get();

        return view('cursos.create', compact(['categories', 'qualifications']));
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
        $qualifications = Qualification::cursoActual()->orderBy('name')->get();

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

        return response('Ok')->header('Content-Type', 'application/json');
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
        $this->addImportId('cursos');
        $this->addImportId('qualifications');
        $this->addImportId('skills');

        $json = $this->cargarFichero('/temp/curso.json');
        $json['slug'] .= '-' . bin2hex(openssl_random_pseudo_bytes(3));
        dump($json);
        factory(Curso::class)->create($json);

        $json = $this->cargarFichero('/temp/qualifications.json');
        dump($json);
        factory(Qualification::class)->createMany($json);

        $json = $this->cargarFichero('/temp/skills.json');
        dump($json);
        factory(Skill::class)->createMany($json);

        $json = $this->cargarFichero('/temp/qualification_skill.json');
        dump($json);
        foreach ($json as $objeto) {
            $qualification = Qualification::where('__import_id', $objeto['qualification_id'])->first();
            $skill = Skill::where('__import_id', $objeto['skill_id'])->first();
            $qualification->skills()->attach($skill, ['percentage' => $objeto['percentage']]);
        }

        $this->removeImportId('cursos');
        $this->removeImportId('qualifications');
        $this->removeImportId('skills');
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
