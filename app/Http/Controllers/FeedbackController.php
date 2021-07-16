<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Curso;
use App\Feedback;
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

        $feedbacks = $curso_actual?->feedbacks()->get() ?? [];

        $actividades = Actividad::cursoActual()->where('plantilla', true)->with(['unidad' => function ($q) {
            $q->orderBy('codigo');
        }])->get();

        return view('feedbacks.index', compact(['feedbacks', 'actividades']));
    }

    public function create()
    {
        $cursos = Curso::orderBy('nombre')->get();

        $curso_actual = Curso::find(setting_usuario('curso_actual'));

        return view('feedbacks.create', compact(['cursos', 'curso_actual']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'curso_id' => 'required',
            'mensaje' => 'required',
        ]);

        Feedback::create($request->all());

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
            'curso_id' => 'required',
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
//        $this->validate($request, [
//            'curso_id' => 'required',
//            'feedback' => 'required',
//        ]);

        Feedback::create([
            'curso_id' => request('tipo') == 'curso' ? request('curso_id') : request('actividad_id'),
            'titulo' => request('titulo'),
            'mensaje' => request('mensaje'),
            'curso_type' => request('tipo') == 'curso' ? 'App\Curso' : 'App\Actividad',
        ]);

        return back();
    }

}
