<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Cuestionario;
use App\Models\Curso;
use App\Models\Pregunta;
use App\Traits\FiltroCurso;
use App\Traits\PaginarUltima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CuestionarioController extends Controller
{
    use PaginarUltima;
    use FiltroCurso;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:profesor|admin')->except(['respuesta']);
    }

    public function index(Request $request)
    {
        $cursos = Curso::orderBy('nombre')->get();

        $cuestionarios = $this->filtrar_por_curso($request, Cuestionario::class)->plantilla()->get();

        return view('cuestionarios.index', compact(['cuestionarios', 'cursos']));
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

        $cuestionario = Cuestionario::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'plantilla' => $request->has('plantilla'),
            'respondido' => $request->has('respondido'),
            'curso_id' => $request->has('curso_id') ? request('curso_id') : Auth::user()->curso_actual()?->id,
        ]);

        olvidar();

        return redirect(route('cuestionarios.edit', ['cuestionario' => $cuestionario]));
    }

    public function show(Cuestionario $cuestionario)
    {
        return view('cuestionarios.show', compact(['cuestionario']));
    }

    public function edit(Cuestionario $cuestionario)
    {
        $preguntas = $cuestionario->preguntas()->orderBy('orden')->get();
        $ids = $preguntas->pluck('id')->toArray();

        return view('cuestionarios.edit', compact(['cuestionario', 'preguntas', 'ids']));
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

        return retornar();
    }

    public function destroy(Cuestionario $cuestionario)
    {
        $cuestionario->delete();

        return back();
    }

    public function actividad(Actividad $actividad)
    {
        $cuestionarios = $actividad->cuestionarios()->get();

        $subset = $cuestionarios->pluck('id')->unique()->flatten()->toArray();
        $curso_actual = Auth::user()->curso_actual()->id;
        $disponibles = $this->paginate_ultima(Cuestionario::where('curso_id', $curso_actual)->where('plantilla', true)->whereNotIn('id', $subset));

        return view('cuestionarios.actividad', compact(['cuestionarios', 'disponibles', 'actividad']));
    }

    public function asociar(Actividad $actividad, Request $request)
    {
        $this->validate($request, [
            'seleccionadas' => 'required',
        ]);

        foreach (request('seleccionadas') as $recurso_id) {
            $recurso = Cuestionario::find($recurso_id);
            $actividad->cuestionarios()->attach($recurso, ['orden' => Str::orderedUuid()]);
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

    public function duplicar(Cuestionario $cuestionario)
    {
        $clon = $cuestionario->duplicate();
        $clon->titulo = $clon->titulo . " (" . __("Copy") . ')';
        $clon->plantilla = $cuestionario->plantilla;
        $clon->save();

        return back();
    }
}
