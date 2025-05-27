<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        $feedbacks = $curso_actual?->feedbacks()->orderBy('orden')->get();
        if (!is_null($feedbacks)) {
            $ids = $feedbacks->pluck('id')->toArray();
        } else {
            $feedbacks = [];
            $ids = [];
        }

        $actividades = Actividad::cursoActual()->where('plantilla', true)->with(['unidad' => function ($q) {
            $q->orderBy('orden');
        }])->with(['feedbacks' => function ($q) {
            $q->orderBy('orden');
        }])->get();

        return view('feedbacks.index', compact(['feedbacks', 'actividades', 'ids']));
    }

    public function create()
    {
        $cursos = Curso::orderBy('nombre')->get();

        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        return view('feedbacks.create', compact(['cursos', 'curso_actual']));
    }

    public function create_actividad(Actividad $actividad)
    {
        return view('feedbacks.create_actividad', compact(['actividad']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'comentable_id' => 'required',
            'mensaje' => 'required',
        ]);

        $request->merge([
            'comentable_type' => Curso::class,
        ]);

        $feedback = Feedback::create($request->all());

        $feedback->orden = $feedback->id;
        $feedback->save();

        return retornar();
    }

    public function show(Feedback $feedback)
    {
        abort(404);
    }

    public function edit(Feedback $feedback)
    {
        $cursos = Curso::orderBy('nombre')->get();

        return view('feedbacks.edit', compact(['feedback', 'cursos']));
    }

    public function update(Request $request, Feedback $feedback)
    {
        $this->validate($request, [
            'comentable_id' => 'required',
            'mensaje' => 'required',
        ]);

        $feedback->update($request->all());

        return retornar();
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return back();
    }

    public function save(Request $request)
    {
        $feedback = Feedback::create([
            'comentable_id' => request('tipo') == 'curso' ? request('curso_id') : request('actividad_id'),
            'comentable_type' => request('tipo') == 'curso' ? Curso::class : Actividad::class,
            'titulo' => request('titulo'),
            'mensaje' => request('mensaje'),
        ]);

        // Quitar los delimitadores tipo === Comentarios (v1) ===
        $feedback->mensaje = preg_replace('/\<p\>===.*===\<\/p\>(\\r\\n)*/', '', (string)$feedback->mensaje);

        $feedback->orden = $feedback->id;
        $feedback->save();

        return retornar(request('from') == 'tarea' ? 0 : 1);
    }

    public function reordenar(Feedback $a1, Feedback $a2)
    {
        $temp = $a1->orden;
        $a1->orden = $a2->orden;
        $a2->orden = $temp;

        $a1->save();
        $a2->save();

        return back();
    }
}
