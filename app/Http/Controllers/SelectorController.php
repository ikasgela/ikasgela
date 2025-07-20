<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Selector;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SelectorController extends Controller
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

        $selectors = $this->filtrar_por_curso($request, Selector::class)->get();

        return view('selectors.index', compact(['selectors', 'cursos']));
    }

    public function create()
    {
        return view('selectors.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        $selector = Selector::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        olvidar();

        return redirect(route('selectors.edit', ['selector' => $selector]));
    }

    public function show(Selector $selector)
    {
        return view('selectors.show', compact(['selector']));
    }

    public function edit(Selector $selector)
    {
        $rule_groups = $selector->rule_groups()->get();
        $ids = $rule_groups->pluck('id')->toArray();

        return view('selectors.edit', compact(['selector', 'rule_groups', 'ids']));
    }

    public function update(Request $request, Selector $selector)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        $selector->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
        ]);

        return retornar();
    }

    public function destroy(Selector $selector)
    {
        $selector->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $selectors = $actividad->selectors()->get();

        $subset = $selectors->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(Selector::where('curso_id', $curso_actual)->whereNotIn('id', $subset));

        return view('selectors.actividad', compact(['selectors', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = Selector::find($recurso_id);
            $actividad->selectors()->attach($recurso, ['orden' => Str::orderedUuid()]);
        }

        return redirect(route('selectors.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, Selector $selector)
    {
        $actividad->selectors()->detach($selector);
        return redirect(route('selectors.actividad', ['actividad' => $actividad->id]));
    }

    public function duplicar(Selector $selector)
    {
        $clon = $selector->duplicate();
        $clon->titulo = $clon->titulo . " (" . __("Copy") . ')';
        $clon->save();

        return redirect(route('selectors.index'));
    }
}
