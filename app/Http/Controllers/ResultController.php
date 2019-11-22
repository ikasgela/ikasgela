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
            $users = $curso->users()->rolAlumno()->orderBy('name')->get();

        // Resultados por competencias

        $skills_curso = [];
        $resultados = [];
        $competencias_50_porciento = true;

        if (!is_null($curso) && !is_null($curso->qualification)) {
            $skills_curso = $curso->qualification->skills;

            foreach ($skills_curso as $skill) {
                $resultados[$skill->id] = new Resultado();
                $resultados[$skill->id]->porcentaje = $skill->pivot->percentage;
            }

            foreach ($user->actividades as $actividad) {

                $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);
                $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);
                $completada = in_array($actividad->tarea->estado, [40, 60]);

                if ($puntuacion_actividad > 0 && $completada) {

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

                    if ($resultados[$skill->id]->tarea / $resultados[$skill->id]->actividad < 0.5)
                        $competencias_50_porciento = false;
                }
            }
        }

        // Nota final
        $nota = 0;
        $porcentaje_total = 0;
        foreach ($resultados as $resultado) {
            if ($resultado->actividad > 0) {
                $nota += ($resultado->tarea / $resultado->actividad) * ($resultado->porcentaje / 100);
                $porcentaje_total += $resultado->porcentaje;
            }
        }

        if ($porcentaje_total == 0)
            $porcentaje_total = 100;

        $nota = $nota / $porcentaje_total * 100;    // Por si el total de competencias suma más del 100%

        // Unidades

        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        // Actividades obligatorias

        $actividades_obligatorias = true;
        $num_actividades_obligatorias = 0;
        foreach ($unidades as $unidad) {
            if ($unidad->num_actividades('base') > 0) {
                $num_actividades_obligatorias += $unidad->num_actividades('base');

                if ($user->num_completadas('base', $unidad->id) < $unidad->num_actividades('base')) {
                    $actividades_obligatorias = false;
                }
            }
        }

        // Ajustar la nota en función de las completadas 100% completadas - 100% de nota

        $numero_actividades_completadas = $user->num_completadas('base');

        $nota = $nota * ($numero_actividades_completadas / $num_actividades_obligatorias);

        $locale = (isset($_COOKIE['locale'])) ? $_COOKIE['locale'] : $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $formatStyle = NumberFormatter::DECIMAL;
        $formatter = new NumberFormatter($locale, $formatStyle);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2);

        $nota_final = $formatter->format($nota * 10);

        // Resultados por unidades

        $resultados_unidades = [];

        foreach ($unidades as $unidad) {
            $resultados_unidades[$unidad->id] = new Resultado();

            foreach ($user->actividades->where('unidad_id', $unidad->id) as $actividad) {

                $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);
                $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);
                $completada = in_array($actividad->tarea->estado, [40, 60]);

                if ($puntuacion_actividad > 0 && $completada) {
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

        // Media de actividades
        $total_actividades_grupo = 0;
        foreach ($users as $usuario) {
            $total_actividades_grupo += $usuario->actividades_completadas()->count();
        }

        $media_actividades_grupo = $formatter->format($total_actividades_grupo / $users->count());

        return view('results.index', compact(['curso', 'skills_curso', 'unidades', 'user', 'users',
            'resultados', 'resultados_unidades', 'nota_final',
            'actividades_obligatorias', 'num_actividades_obligatorias', 'numero_actividades_completadas',
            'pruebas_evaluacion', 'num_pruebas_evaluacion',
            'media_actividades_grupo', 'competencias_50_porciento']));
    }
}
