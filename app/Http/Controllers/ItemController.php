<?php

namespace App\Http\Controllers;

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

        return retornar();
    }

    public function show(Item $item)
    {
        return abort(501);
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

        return retornar();
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return back();
    }

    public function anyadir(Pregunta $pregunta)
    {
        return view('items.anyadir', compact('pregunta'));
    }
}
