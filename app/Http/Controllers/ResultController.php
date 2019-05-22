<?php

namespace App\Http\Controllers;

use App\Curso;
use Auth;
use Setting;

class Resultado
{
    public $actividad;
    public $tarea;
}

class ResultController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $curso = Curso::find(Setting::get($usuario->id . '.curso_actual'));

        $skills_curso = [];
        $resultados = [];

        if (!is_null($curso->qualification)) {
            $skills_curso = $curso->qualification->skills;

            foreach ($skills_curso as $skill) {
                $resultados[$skill->id] = new Resultado();
            }

            foreach ($usuario->actividades as $actividad) {

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

        return view('results.index', compact(['curso', 'skills_curso', 'resultados']));
    }
}
