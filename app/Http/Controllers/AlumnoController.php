<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
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
        $periods = $organization->periods()->with('categories.cursos')->orderBy('slug', 'desc')->get();
        $matricula = Auth::user()->cursos()->dontRemember()->pluck('curso_id')->toArray();
        return view('alumnos.portada', compact(['organization', 'periods', 'matricula']));
    }
}
