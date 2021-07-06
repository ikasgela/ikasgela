<?php

namespace App\Http\Controllers;

use App\Category;
use App\Curso;
use App\Qualification;
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

        $this->exportarFicheroJSON('curso.json', $curso_actual->toJson(JSON_PRETTY_PRINT));
        $this->exportarFicheroJSON('qualifications.json', $curso_actual->qualifications->toJson(JSON_PRETTY_PRINT));
        $this->exportarFicheroJSON('skills.json', $curso_actual->skills->toJson(JSON_PRETTY_PRINT));
        $this->exportarFicheroJSON('qualification_skill.json', DB::table('qualification_skill')->get()->toJson(JSON_PRETTY_PRINT));

        return response('Ok')->header('Content-Type', 'application/json');
    }

    private function exportarFicheroJSON(string $fichero, string $datos): void
    {
        File::put(storage_path('/temp/' . $fichero), $datos);
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

    public function import()
    {
        // Iniciar una transacción

        // Añadir la columna __import_id
        Schema::table('cursos', function (Blueprint $table) {
            $table->bigInteger('__import_id')->unsigned()->nullable();
        });

        // Cargar el fichero
        $path = storage_path() . "/temp/curso.json";
        $json = json_decode(file_get_contents($path), true);

        $json = $this->replaceKeys('id', '__import_id', $json);

        dump($json);

        // Recorrerlo
        factory(Curso::class)->create($json);

        // Quitar la columna
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('__import_id');
        });

        // Terminar la transacción
    }
}
