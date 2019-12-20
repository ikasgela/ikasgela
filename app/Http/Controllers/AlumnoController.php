<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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

        // Recuento de asignadas
        $num_actividades = $user->actividades_asignadas()->count();

        if ($num_actividades > 0)
            session(['num_actividades' => $num_actividades]);
        else
            session()->forget('num_actividades');

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
