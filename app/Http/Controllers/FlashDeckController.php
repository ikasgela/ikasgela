<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\FlashDeck;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FlashDeckController extends Controller
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

        $flash_decks = $this->filtrar_por_curso($request, FlashDeck::class)->plantilla()->get();

        return view('flash_decks.index', compact(['flash_decks', 'cursos']));
    }

    public function create()
    {
        return view('flash_decks.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        FlashDeck::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'completado' => $request->has('completado'),
            'posicion' => $request->input('posicion'),
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        return retornar();
    }

    public function show(FlashDeck $flash_deck)
    {
        return view('flash_decks.show', compact(['flash_deck']));
    }

    public function edit(FlashDeck $flash_deck)
    {
        return view('flash_decks.edit', compact(['flash_deck']));
    }

    public function update(Request $request, FlashDeck $flash_deck)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        $flash_deck->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'completado' => $request->has('completado'),
            'posicion' => $request->input('posicion'),
        ]);

        return retornar();
    }

    public function destroy(FlashDeck $flash_deck)
    {
        $flash_deck->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $flash_decks = $actividad->flash_decks()->get();

        $subset = $flash_decks->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(FlashDeck::where('curso_id', $curso_actual)->where('plantilla', true)->whereNotIn('id', $subset));

        return view('flash_decks.actividad', compact(['flash_decks', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = FlashDeck::find($recurso_id);
            $actividad->flash_decks()->attach($recurso, [
                'orden' => Str::orderedUuid(),
                'columnas' => 12,
            ]);
        }

        return redirect(route('flash_decks.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, FlashDeck $flash_deck)
    {
        $actividad->flash_decks()->detach($flash_deck);
        return redirect(route('flash_decks.actividad', ['actividad' => $actividad->id]));
    }

    public function duplicar(FlashDeck $flash_deck)
    {
        $flash_deck->duplicar(null);

        return redirect(route('flash_decks.index'));
    }
}
