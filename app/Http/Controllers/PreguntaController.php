<?php

namespace App\Http\Controllers;

use App\Cuestionario;
use App\Pregunta;
use BadMethodCallException;
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
        $preguntas = Pregunta::all();

        return view('preguntas.index', compact('preguntas'));
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

        Pregunta::create([
            'cuestionario_id' => $request->input('cuestionario_id'),
            'titulo' => $request->input('titulo'),
            'texto' => $request->input('texto'),
            'multiple' => $request->has('multiple'),
            'respondida' => $request->has('respondida'),
            'correcta' => $request->has('correcta'),
            'imagen' => $request->input('imagen'),
        ]);

        if (!is_null($request->input('accion'))) {
            switch ($request->input('accion')) {
                case 'preguntas.anyadir':
                    return redirect(route('cuestionarios.edit', ['cuestionario_id' => $request->input('cuestionario_id')]));
            }
        }
        return redirect(route('preguntas.index'));
    }

    public function show(Pregunta $pregunta)
    {
        throw new BadMethodCallException(__('Not implemented.'));
    }

    public function edit(Pregunta $pregunta)
    {
        $cuestionarios = Cuestionario::orderBy('titulo')->get();

        return view('preguntas.edit', compact(['pregunta', 'cuestionarios']));
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

        return redirect(route('preguntas.index'));
    }

    public function destroy(Pregunta $pregunta)
    {
        $pregunta->delete();

        return redirect()->back();
    }

    public function anyadir(Cuestionario $cuestionario)
    {
        return view('preguntas.anyadir', compact('cuestionario'));
    }
}
