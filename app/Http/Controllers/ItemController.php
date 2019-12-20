<?php

namespace App\Http\Controllers;

use App\Cuestionario;
use App\Item;
use App\Pregunta;
use BadMethodCallException;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor');
    }

    public function index()
    {
        $items = Item::all();

        return view('items.index', compact('items'));
    }

    public function create()
    {
        $preguntas = Pregunta::orderBy('titulo')->get();

        return view('items.create', compact('preguntas'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'texto' => 'required',
            'pregunta_id' => 'required',
        ]);

        Item::create([
            'pregunta_id' => $request->input('pregunta_id'),
            'texto' => $request->input('texto'),
            'correcto' => $request->has('correcto'),
            'seleccionado' => $request->has('seleccionado'),
            'feedback' => $request->input('feedback'),
            'orden' => $request->input('orden'),
        ]);

        if (!is_null($request->input('accion'))) {
            switch ($request->input('accion')) {
                case 'items.anyadir':
                    return redirect(route('preguntas.edit', ['pregunta' => $request->input('pregunta_id')]));
            }
        }
        return redirect(route('items.index'));
    }

    public function show(Item $item)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Item $item)
    {
        $preguntas = Pregunta::orderBy('titulo')->get();

        return view('items.edit', compact(['item', 'preguntas']));
    }

    public function update(Request $request, Item $item)
    {
        $this->validate($request, [
            'texto' => 'required',
            'pregunta_id' => 'required',
        ]);

        $item->update([
            'pregunta_id' => $request->input('pregunta_id'),
            'texto' => $request->input('texto'),
            'correcto' => $request->has('correcto'),
            'seleccionado' => $request->has('seleccionado'),
            'feedback' => $request->input('feedback'),
            'orden' => $request->input('orden'),
        ]);

        return redirect(route('items.index'));
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect(route('items.index'));
    }

    public function anyadir(Pregunta $pregunta)
    {
        return view('items.anyadir', compact('pregunta'));
    }
}
