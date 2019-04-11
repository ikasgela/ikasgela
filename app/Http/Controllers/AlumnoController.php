<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

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
         * 30 -> Enviada
         * 40 -> Revisada: OK
         * 41 -> Revisada: ERROR
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

        $actividades = $user->actividades_asignadas();

        return view('alumnos.tareas', compact(['actividades', 'user']));
    }
}
