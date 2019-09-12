<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Cuestionario;
use App\Pregunta;
use Illuminate\Http\Request;

class CuestionarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor')->except(['respuesta']);
    }

    public function index()
    {
        $cuestionarios = Cuestionario::plantilla()->get();

        return view('cuestionarios.index', compact('cuestionarios'));
    }

    public function create()
    {
        return view('cuestionarios.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        Cuestionario::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'respondido' => $request->has('respondido'),
        ]);

        return redirect(route('cuestionarios.index'));
    }

    public function show(Cuestionario $cuestionario)
    {
        return view('cuestionarios.show', compact(['cuestionario']));
    }

    public function edit(Cuestionario $cuestionario)
    {
        $preguntas = $cuestionario->preguntas;
        return view('cuestionarios.edit', compact(['cuestionario', 'preguntas']));
    }

    public function update(Request $request, Cuestionario $cuestionario)
    {
        $this->validate($request, [
            'titulo' => 'required',
        ]);

        $cuestionario->update([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'respondido' => $request->has('respondido'),
        ]);

        return back();
    }

    public function destroy(Cuestionario $cuestionario)
    {
        $cuestionario->delete();

        return redirect(route('cuestionarios.index'));
    }

    public function actividad(Actividad $actividad)
    {
        $cuestionarios = $actividad->cuestionarios()->get();

        $subset = $cuestionarios->pluck('id')->unique()->flatten()->toArray();
        $disponibles = Cuestionario::where('plantilla', true)->whereNotIn('id', $subset)->get();

        return view('cuestionarios.actividad', compact(['cuestionarios', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = Cuestionario::find($recurso_id);
            $actividad->cuestionarios()->attach($recurso);
        }

        return redirect(route('cuestionarios.actividad', ['actividad' => $actividad->id]));
    }

    public function desasociar(Actividad $actividad, Cuestionario $cuestionario)
    {
        $actividad->cuestionarios()->detach($cuestionario);
        return redirect(route('cuestionarios.actividad', ['actividad' => $actividad->id]));
    }

    public function respuesta(Request $request, Cuestionario $cuestionario)
    {
        $this->validate($request, [
            'respuestas' => 'required',
        ]);

        foreach ($request->input('respuestas') as $pregunta_id => $values) {

            $correcta = true;
            $num_correctas = 0;

            $pregunta = Pregunta::find($pregunta_id);

            foreach ($pregunta->items as $item) {

                if (in_array($item->id, $values)) {
                    if (!$item->correcto)
                        $correcta = false;
                    else
                        $num_correctas += 1;
                    $item->seleccionado = true;
                    $item->save();
                } else {
                    if ($item->correcto)
                        $correcta = false;
                }
            }

            if (!$pregunta->multiple && $num_correctas > 0) {
                $correcta = true;
            }

            $pregunta->respondida = true;
            $pregunta->correcta = $correcta;
            $pregunta->save();
        }

        $cuestionario->respondido = true;
        $cuestionario->save();

        return redirect()->back();
    }
}
