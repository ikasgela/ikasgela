<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $sebs_url = "";
        $sebs_exit_url = "";
        if (!is_null($user->curso_actual())) {
            $sebs_url = route('safe_exam.config_seb', $user->curso_actual());
            $sebs_url = Str::replace("http", "seb", $sebs_url);
            $sebs_exit_url = route('safe_exam.exit_seb', hash("sha256", $user->curso_actual()->safe_exam?->quit_password));
        }

        return view('alumnos.tareas', compact([
            'user',
            'sebs_url', 'sebs_exit_url',
        ]));
    }

    public function portada(Request $request)
    {
        $organization = Organization::where('slug', subdominio())->first();
        $periods = $organization->periods()->with('categories.cursos')->orderBy('slug', 'desc')->get();
        $matricula = Auth::user()->cursos()->dontRemember()->pluck('curso_id')->toArray();
        return view('alumnos.portada', compact(['organization', 'periods', 'matricula']));
    }
}
