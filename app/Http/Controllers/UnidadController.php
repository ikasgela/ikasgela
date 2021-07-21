<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Qualification;
use App\Models\Unidad;
use App\Traits\FiltroCurso;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnidadController extends Controller
{
    use FiltroCurso;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $unidades = $this->filtrar_por_curso($request, Unidad::class, 'orden')->get();

        $ids = $unidades->pluck('id')->toArray();

        return view('unidades.index', compact(['unidades', 'cursos', 'ids']));
    }

    public function create()
    {
        $cursos = Curso::orderBy('nombre')->get();
        $qualifications = Qualification::cursoActual()->orderBy('name')->get();

        return view('unidades.create', compact(['cursos', 'qualifications']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'nombre' => 'required',
        ]);

        $unidad = Unidad::create([
            'curso_id' => request('curso_id'),
            'codigo' => request('codigo'),
            'nombre' => request('nombre'),
            'descripcion' => request('descripcion'),
            'slug' => Str::slug(request('nombre')),
            'qualification_id' => request('qualification_id'),
            'tags' => request('tags'),
            'fecha_disponibilidad' => request('fecha_disponibilidad'),
            'fecha_entrega' => request('fecha_entrega'),
            'fecha_limite' => request('fecha_limite'),
            'minimo_entregadas' => request('minimo_entregadas'),
        ]);

        $unidad->orden = $unidad->id;
        $unidad->save();

        return retornar();
    }

    public function show(Unidad $unidad)
    {
        abort(404);
    }

    public function edit(Unidad $unidad)
    {
        $cursos = Curso::orderBy('nombre')->get();
        $qualifications = $unidad->curso->qualifications()->orderBy('name')->get();

        return view('unidades.edit', compact(['unidad', 'cursos', 'qualifications']));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'nombre' => 'required',
        ]);

        $unidad->update([
            'curso_id' => request('curso_id'),
            'codigo' => request('codigo'),
            'nombre' => request('nombre'),
            'descripcion' => request('descripcion'),
            'slug' => strlen(request('slug')) > 0
                ? Str::slug(request('slug'))
                : Str::slug(request('nombre')),
            'qualification_id' => request('qualification_id'),
            'orden' => request('orden'),
            'tags' => request('tags'),
            'fecha_disponibilidad' => request('fecha_disponibilidad'),
            'fecha_entrega' => request('fecha_entrega'),
            'fecha_limite' => request('fecha_limite'),
            'minimo_entregadas' => request('minimo_entregadas'),
        ]);

        return retornar();
    }

    public function destroy(Unidad $unidad)
    {
        $unidad->delete();

        return back();
    }

    public function reordenar(Unidad $a1, Unidad $a2)
    {
        $temp = $a1->orden;
        $a1->orden = $a2->orden;
        $a2->orden = $temp;

        $a1->save();
        $a2->save();

        return back();
    }
}
