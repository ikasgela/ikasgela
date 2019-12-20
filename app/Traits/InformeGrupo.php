<?php

namespace App\Traits;

use App\Curso;
use App\Http\Controllers\Resultado;
use App\Organization;
use App\Unidad;
use Illuminate\Http\Request;
use NumberFormatter;

trait InformeGrupo
{
    public function datosInformeGrupo(Request $request, $exportar = false)
    {
        $organization = Organization::find(setting_usuario('_organization_id'));

        $curso = Curso::find(setting_usuario('curso_actual'));

        if ($request->has('filtro_alumnos')) {
            session(['tutor_filtro_alumnos' => $request->input('filtro_alumnos')]);
        }

        switch (session('tutor_filtro_alumnos')) {
            case 'P':
                $usuarios = $curso->users()->rolAlumno()->noBloqueado()->orderBy('name')->get()->sortBy('actividades_completadas');
                break;
            default:
                $usuarios = $curso->users()->rolAlumno()->noBloqueado()->orderBy('name')->get();
                break;
        }

        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        // Total de actividades para el cálculo de la media
        $total_actividades_grupo = 0;
        foreach ($usuarios as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base');
        }

        // Resultados por usuario y unidades

        $resultados_usuario_unidades = [];

        foreach ($usuarios as $usuario) {

            foreach ($unidades as $unidad) {
                $resultados_usuario_unidades[$usuario->id][$unidad->id] = new Resultado();

                foreach ($usuario->actividades->where('unidad_id', $unidad->id) as $actividad) {

                    $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);
                    $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);
                    $completada = in_array($actividad->tarea->estado, [40, 60]);

                    if ($puntuacion_actividad > 0 && $completada) {
                        $resultados_usuario_unidades[$usuario->id][$unidad->id]->actividad += $puntuacion_actividad;
                        $resultados_usuario_unidades[$usuario->id][$unidad->id]->tarea += $puntuacion_tarea;
                    }
                }
            }
        }

        // Formateador con 2 decimales y en el idioma del usuario
        $locale = (isset($_COOKIE['locale'])) ? $_COOKIE['locale'] : $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $formatStyle = NumberFormatter::DECIMAL;

        if (!$exportar)
            $formatter = new NumberFormatter($locale, $formatStyle);
        else
            $formatter = new NumberFormatter("en_US", $formatStyle);

        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);

        // Actividades obligatorias

        $num_actividades_obligatorias = 0;
        foreach ($unidades as $unidad) {
            if ($unidad->num_actividades('base') > 0) {
                $num_actividades_obligatorias += $unidad->num_actividades('base');
            }
        }

        // Notas finales
        $notas = [];

        $actividades_obligatorias = [];

        $pruebas_evaluacion = [];
        $num_pruebas_evaluacion = [];

        $competencias_50_porciento = [];

        foreach ($usuarios as $user) {

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

                        $competencias_50_porciento[$user->id] = true;
                        if ($resultados[$skill->id]->tarea / $resultados[$skill->id]->actividad < 0.5)
                            $competencias_50_porciento[$user->id] = false;
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

            // Ajustar la nota en función de las completadas 100% completadas - 100% de nota
            $numero_actividades_completadas = $user->num_completadas('base');
            $nota = $nota * ($numero_actividades_completadas / $num_actividades_obligatorias) * 10;

            $notas[$user->id] = $formatter->format($nota / $porcentaje_total * 100);    // Por si el total de competencias suma más del 100%

            $actividades_obligatorias[$user->id] = true;
            if ($numero_actividades_completadas < $num_actividades_obligatorias) {
                $actividades_obligatorias[$user->id] = false;
            }

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

            $pruebas_evaluacion[$user->id] = true;
            $num_pruebas_evaluacion[$user->id] = 0;

            foreach ($unidades as $unidad) {
                if ($unidad->hasEtiqueta('examen')
                    && $user->num_completadas('examen', $unidad->id) > 0
                    && $resultados_unidades[$unidad->id]->actividad > 0) {
                    $num_pruebas_evaluacion[$user->id] += 1;

                    if (($resultados_unidades[$unidad->id]->tarea / $resultados_unidades[$unidad->id]->actividad) * 10 < 5) {
                        $pruebas_evaluacion[$user->id] = false;
                    }
                }
            }

        }

        $media_actividades_grupo = $total_actividades_grupo / $usuarios->count();
        $media_actividades_grupo_formato = $formatter->format($media_actividades_grupo);

        return compact(['usuarios', 'unidades', 'organization',
            'total_actividades_grupo', 'resultados_usuario_unidades', 'curso',
            'media_actividades_grupo', 'media_actividades_grupo_formato', 'notas', 'actividades_obligatorias', 'num_actividades_obligatorias',
            'pruebas_evaluacion', 'num_pruebas_evaluacion', 'competencias_50_porciento']);
    }
}
