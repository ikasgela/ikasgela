<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Rubric;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RubricController extends Controller
{
    use PaginarUltima;
    use FiltroCurso;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor|admin');
    }

    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $rubrics = $this->filtrar_por_curso($request, Rubric::class)->plantilla()->get();

        return view('rubrics.index', compact(['rubrics', 'cursos']));
    }

    public function create()
    {
        return view('rubrics.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        Rubric::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'completada' => $request->has('completada'),
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        return retornar();
    }

    public function show(Rubric $rubric)
    {
        return view('rubrics.show', compact(['rubric']));
    }

    public function edit(Rubric $rubric)
    {
        return view('rubrics.edit', compact(['rubric']));
    }

    public function update(Request $request, Rubric $rubric)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        $rubric->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'completada' => $request->has('completada'),
        ]);

        return retornar();
    }

    public function destroy(Rubric $rubric)
    {
        $rubric->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $rubrics = $actividad->rubrics()->get();

        $subset = $rubrics->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(Rubric::where('curso_id', $curso_actual)->where('plantilla', true)->whereNotIn('id', $subset));

        return view('rubrics.actividad', compact(['rubrics', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = Rubric::find($recurso_id);
            $actividad->rubrics()->attach($recurso, [
                'orden' => Str::orderedUuid(),
                'columnas' => 12,
            ]);
        }

        return redirect(route('rubrics.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, Rubric $rubric)
    {
        $actividad->rubrics()->detach($rubric);
        return redirect(route('rubrics.actividad', ['actividad' => $actividad->id]));
    }

    public function duplicar(Rubric $rubric)
    {
        $clon = $rubric->duplicate();
        $clon->titulo = $clon->titulo . " (" . __("Copy") . ')';
        $clon->plantilla = $rubric->plantilla;
        $clon->save();

        return back();
    }
}
