<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Unidad;
use App\User;
use Auth;
use Illuminate\Http\Request;

class Resultado
{
    public $actividad;
    public $tarea;
}

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!empty($request->input('user_id'))) {
            $user = User::find($request->input('user_id'));
            session(['filtrar_user_actual' => $request->input('user_id')]);
        } else {
            session()->forget('filtrar_user_actual');
        }

        // Lista de usuarios
        $curso = Curso::find(setting_usuario('curso_actual'));
        $users = null;
        if (!is_null($curso))
            $users = $curso->users()->orderBy('name')->get();

        // Resultados por competencias

        $skills_curso = [];
        $resultados = [];

        if (!is_null($curso) && !is_null($curso->qualification)) {
            $skills_curso = $curso->qualification->skills;

            foreach ($skills_curso as $skill) {
                $resultados[$skill->id] = new Resultado();
            }

            foreach ($user->actividades as $actividad) {

                $puntuacion_actividad = $actividad->puntuacion;
                $puntuacion_tarea = $actividad->tarea->puntuacion;

                if ($puntuacion_actividad > 0) {

                    if (!is_null($actividad->qualification_id)) {
                        $skills = $actividad->qualification->skills;
                    } else if (!is_null($actividad->unidad->qualification_id)) {
                        $skills = $actividad->unidad->qualification->skills;
                    } else {
                        $skills = $skills_curso;
                    }

                    foreach ($skills as $skill) {
                        $porcentaje = $skill->pivot->percentage;
                        $resultados[$skill->id]->actividad += $puntuacion_actividad * $porcentaje / 100;
                        $resultados[$skill->id]->tarea += $puntuacion_tarea * $porcentaje / 100;
                    }
                }
            }
        }

        // Resultados por unidades

        $unidades = Unidad::cursoActual()->orderBy('codigo')->orderBy('nombre')->get();

        // Resultados por competencias

        $resultados_unidades = [];

        foreach ($unidades as $unidad) {
            $resultados_unidades[$unidad->id] = new Resultado();

            foreach ($user->actividades->where('unidad_id', $unidad->id) as $actividad) {

                if ($puntuacion_actividad > 0) {

                    $puntuacion_actividad = $actividad->puntuacion;
                    $puntuacion_tarea = $actividad->tarea->puntuacion;

                    $resultados_unidades[$unidad->id]->actividad += $puntuacion_actividad;
                    $resultados_unidades[$unidad->id]->tarea += $puntuacion_tarea;

                }
            }
        }

        return view('results.index', compact(['curso', 'skills_curso', 'resultados', 'unidades', 'user', 'users', 'resultados_unidades']));
    }
}
