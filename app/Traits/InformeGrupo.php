<?php

namespace App\Traits;

use App\Curso;
use App\Http\Controllers\Resultado;
use App\Organization;
use App\Unidad;
use Illuminate\Http\Request;

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
                $usuarios = $curso->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get()->sortBy('num_completadas_base');
                break;
            default:
                $usuarios = $curso->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get();
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

        $evaluacion_continua_superada = [];

        foreach ($usuarios as $user) {

            // Resultados por competencias

            $skills_curso = [];
            $resultados = [];

            if (!is_null($curso) && !is_null($curso->qualification)) {
                $skills_curso = $curso->qualification->skills;

                foreach ($skills_curso as $skill) {
                    $resultados[$skill->id] = new Resultado();
                    $resultados[$skill->id]->porcentaje = $skill->pivot->percentage;
                }

                foreach ($user->actividades_completadas()->get() as $actividad) {

                    // Total de puntos de la actividad
                    $puntuacion_actividad = $actividad->puntuacion * ($actividad->multiplicador ?: 1);

                    // Puntos obtenidos
                    $puntuacion_tarea = $actividad->tarea->puntuacion * ($actividad->multiplicador ?: 1);

                    if ($puntuacion_actividad > 0) {

                        // Obtener las competencias: Curso->Unidad->Actividad
                        if (!is_null($actividad->qualification_id)) {
                            $skills = $actividad->qualification->skills;
                        } else if (!is_null($actividad->unidad->qualification_id)) {
                            $skills = $actividad->unidad->qualification->skills;
                        } else {
                            $skills = $skills_curso;
                        }

                        foreach ($skills as $skill) {

                            // Aportación de la competencia a la cualificación
                            $porcentaje = $skill->pivot->percentage;

                            // Peso relativo de las actividades de examen
                            $peso_examen = $skill->peso_examen;
                            $peso_tarea = 100 - $skill->peso_examen;

                            $resultados[$skill->id]->peso_examen = $skill->peso_examen;

                            if ($actividad->hasEtiqueta('base')) {
                                $resultados[$skill->id]->puntos_tarea += $puntuacion_tarea * ($porcentaje / 100);
                                $resultados[$skill->id]->puntos_totales_tarea += $puntuacion_actividad * ($porcentaje / 100);
                                $resultados[$skill->id]->num_tareas += 1;
                            } else if ($actividad->hasEtiqueta('examen')) {
                                $resultados[$skill->id]->puntos_examen += $puntuacion_tarea * ($porcentaje / 100);
                                $resultados[$skill->id]->puntos_totales_examen += $puntuacion_actividad * ($porcentaje / 100);
                                $resultados[$skill->id]->num_examenes += 1;
                            } else if ($actividad->hasEtiqueta('extra') || $actividad->hasEtiqueta('repaso')) {
                                $resultados[$skill->id]->puntos_tarea += $puntuacion_tarea * ($porcentaje / 100);
                                $resultados[$skill->id]->num_tareas += 1;
                            }

                            $resultados[$skill->id]->tarea += $puntuacion_tarea * ($porcentaje / 100);
                            $resultados[$skill->id]->actividad += $puntuacion_actividad * ($porcentaje / 100);
                        }
                    }
                }
            }

            // Aplicar el criterio del mínimo de competencias
            $competencias_50_porciento[$user->id] = true;
            $minimo_competencias = $curso->minimo_competencias;
            foreach ($resultados as $resultado) {
                if ($resultado->porcentaje_competencia() < $minimo_competencias)
                    $competencias_50_porciento[$user->id] = false;
            }

            // Nota final
            $nota = 0;
            foreach ($resultados as $resultado) {
                if ($resultado->actividad > 0) {
                    $nota += ($resultado->porcentaje_competencia() / 100) * ($resultado->porcentaje / 100);
                }
            }

            // Actividades obligatorias

            $minimo_entregadas = $curso->minimo_entregadas;

            $actividades_obligatorias[$user->id] = true;
            $num_actividades_obligatorias = 0;
            foreach ($unidades as $unidad) {
                if ($unidad->num_actividades('base') > 0) {
                    $num_actividades_obligatorias += $unidad->num_actividades('base');

                    if ($user->num_completadas('base', $unidad->id) < $unidad->num_actividades('base') * $minimo_entregadas / 100) {
                        $actividades_obligatorias[$user->id] = false;
                    }
                }
            }

            // Ajustar la nota en función de las completadas 100% completadas - 100% de nota
            $numero_actividades_completadas = $user->num_completadas('base');
            if ($num_actividades_obligatorias > 0)
                $nota = $nota * ($numero_actividades_completadas / $num_actividades_obligatorias) * 10;

            $notas[$user->id] = formato_decimales($nota, 2, $exportar);

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

            $minimo_examenes = $curso->minimo_examenes;

            $pruebas_evaluacion[$user->id] = true;
            $num_pruebas_evaluacion[$user->id] = 0;
            foreach ($unidades as $unidad) {
                if ($unidad->hasEtiqueta('examen')
                    && $user->num_completadas('examen', $unidad->id) > 0
                    && $resultados_unidades[$unidad->id]->actividad > 0) {
                    $num_pruebas_evaluacion[$user->id] += 1;

                    if (($resultados_unidades[$unidad->id]->tarea / $resultados_unidades[$unidad->id]->actividad) < $minimo_examenes / 100) {
                        $pruebas_evaluacion[$user->id] = false;
                    }
                }
            }

            // Evaluación continua

            $evaluacion_continua_superada[$user->id] = ($actividades_obligatorias[$user->id] || $num_actividades_obligatorias == 0 || $curso->minimo_entregadas == 0)
                && (!$curso->examenes_obligatorios || $pruebas_evaluacion[$user->id] || $num_pruebas_evaluacion[$user->id] == 0)
                && $competencias_50_porciento[$user->id] && $notas[$user->id] >= 5;
        }

        $media_actividades_grupo = $usuarios->count() > 0 ? $total_actividades_grupo / $usuarios->count() : 0;
        $media_actividades_grupo_formato = formato_decimales($media_actividades_grupo, 2, $exportar);

        return compact(['usuarios', 'unidades', 'organization',
            'total_actividades_grupo', 'resultados_usuario_unidades', 'curso',
            'media_actividades_grupo', 'media_actividades_grupo_formato', 'notas', 'actividades_obligatorias', 'num_actividades_obligatorias',
            'pruebas_evaluacion', 'num_pruebas_evaluacion', 'competencias_50_porciento', 'evaluacion_continua_superada']);
    }
}
