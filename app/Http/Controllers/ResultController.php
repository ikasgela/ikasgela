<?php

namespace App\Http\Controllers;

use App\Curso;
use Auth;

class Resultado
{
    public $actividad;
    public $tarea;
}

class ResultController extends Controller
{
    public function index()
    {
        $curso = Curso::find(1);    // TODO: Seleccionar el curso actual
        $usuario = Auth::user();

        $skills_curso = $curso->qualification->skills;

        $skills = $curso->qualification->skills;

        $resultados = [];
        foreach ($skills as $skill) {
            $resultados[$skill->id] = new Resultado();
        }

        foreach ($usuario->actividades as $actividad) {

            if ($actividad->puntuacion > 0) {

                if (!is_null($actividad->qualification_id)) {
                    $skills = $actividad->qualification->skills;
                }

                $total = $actividad->puntuacion;
                $tarea = $actividad->tarea->puntuacion;

                foreach ($skills as $skill) {
                    $porcentaje = $skill->pivot->percentage;
                    $resultados[$skill->id]->actividad += $total * $porcentaje / 100;
                    $resultados[$skill->id]->tarea += $tarea * $porcentaje / 100;
                }
            }
        }

        return view('results.index', compact(['curso', 'skills_curso', 'resultados']));
    }
}
