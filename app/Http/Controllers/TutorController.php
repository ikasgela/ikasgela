<?php

namespace App\Http\Controllers;

use App\Curso;
use App\Organization;
use App\Tarea;
use App\Unidad;
use App\User;
use Illuminate\Http\Request;
use NumberFormatter;

class TutorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        memorizar_ruta();

        $this->recuento_enviadas();

        $organization = Organization::find(setting_usuario('_organization_id'));

        $curso = Curso::find(setting_usuario('curso_actual'));

        $usuarios = $curso->users()->rolAlumno()->noBloqueado()->orderBy('name')->get();

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
        $formatter = new NumberFormatter($locale, $formatStyle);
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
        }

        $media_actividades_grupo = $formatter->format($total_actividades_grupo / $usuarios->count());

        return view('tutor.index', compact(['usuarios', 'unidades', 'organization',
            'total_actividades_grupo', 'resultados_usuario_unidades', 'curso',
            'media_actividades_grupo', 'notas']));
    }

    private function recuento_enviadas(): void
    {
        $tareas = Tarea::cursoActual()->noAutoAvance()->where('estado', 30)->get();

        $num_enviadas = count($tareas);
        if ($num_enviadas > 0)
            session(['num_enviadas' => $num_enviadas]);
        else
            session()->forget('num_enviadas');
    }

}
