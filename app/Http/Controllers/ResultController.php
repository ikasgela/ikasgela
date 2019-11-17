<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Unidad;
use App\User;
use Auth;
use Illuminate\Http\Request;
use NumberFormatter;

class Resultado
{
    public $actividad;
    public $tarea;
    public $porcentaje;
}

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!empty($request->input('user_id')) && $request->input('user_id') != -1) {
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
                $resultados[$skill->id]->porcentaje = $skill->pivot->percentage;
            }

            foreach ($user->actividades as $actividad) {

                $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);
                $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);

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

        // Nota final
        $nota = 0;
        foreach ($resultados as $resultado) {
            if ($resultado->actividad > 0)
                $nota += ($resultado->tarea / $resultado->actividad) * ($resultado->porcentaje / 100);
        }

        $locale = (isset($_COOKIE['locale'])) ? $_COOKIE['locale'] : $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $formatStyle = NumberFormatter::DECIMAL;
        $formatter = new NumberFormatter($locale, $formatStyle);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2);

        $nota_final = $formatter->format($nota * 10);

        // Unidades

        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        // Actividades obligatorias

        $actividades_obligatorias = true;
        $num_actividades_obligatorias = 0;
        foreach ($unidades as $unidad) {
            if ($unidad->num_actividades('base') > 0) {
                $num_actividades_obligatorias += 1;

                if ($user->num_completadas('base', $unidad->id) < $unidad->num_actividades('base')) {
                    $actividades_obligatorias = false;
                }
            }
        }

        // Resultados por unidades

        $resultados_unidades = [];

        foreach ($unidades as $unidad) {
            $resultados_unidades[$unidad->id] = new Resultado();

            foreach ($user->actividades->where('unidad_id', $unidad->id) as $actividad) {

                $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);
                $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);

                if ($puntuacion_actividad > 0) {
                    $resultados_unidades[$unidad->id]->actividad += $puntuacion_actividad;
                    $resultados_unidades[$unidad->id]->tarea += $puntuacion_tarea;
                }
            }
        }

        // Pruebas de evaluación

        $pruebas_evaluacion = false;
        $num_pruebas_evaluacion = 0;
        foreach ($unidades as $unidad) {
            if ($unidad->hasEtiqueta('examen')
                && $user->num_completadas('examen', $unidad->id) > 0
                && $resultados_unidades[$unidad->id]->actividad > 0) {
                $num_pruebas_evaluacion += 1;

                if (($resultados_unidades[$unidad->id]->tarea / $resultados_unidades[$unidad->id]->actividad) * 10 >= 5) {
                    $pruebas_evaluacion = true;
                }
            }
        }

        return view('results.index', compact(['curso', 'skills_curso', 'unidades', 'user', 'users',
            'resultados', 'resultados_unidades', 'nota_final',
            'actividades_obligatorias', 'num_actividades_obligatorias', 'pruebas_evaluacion', 'num_pruebas_evaluacion']));
    }
}
