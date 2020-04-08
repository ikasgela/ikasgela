<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Setting;

class AlumnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function tareas()
    {
        /*
         * 10 -> Nueva
         * 11 -> Oculta
         * 20 -> Aceptada
         * 21 -> Feedback leído
         * 30 -> Enviada
         * 31 -> Reiniciada
         * 40 -> Revisada: OK
         * 41 -> Revisada: ERROR
         * 42 -> Avance automático
         * 50 -> Terminada
         * 60 -> Archivada
         * */

        $user = Auth::user();

        return view('alumnos.tareas', compact(['user']));
    }

    public function portada(Request $request)
    {
        $organization = Organization::where('slug', subdominio())->first();
        $period = $organization->periods()->where('slug', '2019')->first();
        return view('alumnos.portada', compact(['organization', 'period']));
    }

    public function setCurso(Curso $curso, Request $request)
    {
        $user = Auth::user();
        $cursos = Curso::all();

        Setting::set($user->id . '.curso_actual', $request->input('curso_id'));

        return view('alumnos.portada', compact(['cursos']));
    }
}
