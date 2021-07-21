<?php

namespace App\Http\Controllers;

use App\Models\Cuestionario;
use App\Models\Pregunta;
use Illuminate\Http\Request;

class PreguntaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor');
    }

    public function index()
    {
        abort(404);
    }

    public function create()
    {
        $cuestionarios = Cuestionario::orderBy('titulo')->get();

        return view('preguntas.create', compact('cuestionarios'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'texto' => 'required',
            'cuestionario_id' => 'required',
        ]);

        $pregunta = Pregunta::create([
            'cuestionario_id' => $request->input('cuestionario_id'),
            'titulo' => $request->input('titulo'),
            'texto' => $request->input('texto'),
            'multiple' => $request->has('multiple'),
            'respondida' => $request->has('respondida'),
            'correcta' => $request->has('correcta'),
            'imagen' => $request->input('imagen'),
        ]);

        $pregunta->orden = $pregunta->id;
        $pregunta->save();

        olvidar();

        return redirect(route('preguntas.edit', ['pregunta' => $pregunta]));
    }

    public function show(Pregunta $pregunta)
    {
        abort(404);
    }

    public function edit(Pregunta $pregunta)
    {
        $cuestionarios = Cuestionario::orderBy('titulo')->get();

        $items = $pregunta->items;
        $ids = $items->pluck('id')->toArray();

        return view('preguntas.edit', compact(['pregunta', 'cuestionarios', 'items', 'ids']));
    }

    public function update(Request $request, Pregunta $pregunta)
    {
        $this->validate($request, [
            'titulo' => 'required',
            'texto' => 'required',
            'cuestionario_id' => 'required',
        ]);

        $pregunta->update([
            'cuestionario_id' => $request->input('cuestionario_id'),
            'titulo' => $request->input('titulo'),
            'texto' => $request->input('texto'),
            'multiple' => $request->has('multiple'),
            'respondida' => $request->has('respondida'),
            'correcta' => $request->has('correcta'),
            'imagen' => $request->input('imagen'),
        ]);

        return retornar();
    }

    public function destroy(Pregunta $pregunta)
    {
        $pregunta->delete();

        return back();
    }

    public function anyadir(Cuestionario $cuestionario)
    {
        return view('preguntas.anyadir', compact('cuestionario'));
    }

    public function reordenar(Pregunta $a1, Pregunta $a2)
    {
        $temp = $a1->orden;
        $a1->orden = $a2->orden;
        $a2->orden = $temp;

        $a1->save();
        $a2->save();

        return back();
    }
}
