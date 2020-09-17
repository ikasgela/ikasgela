<?php

namespace App\Http\Controllers;

use App\Charts\TareasEnviadas;
use App\Curso;
use App\Registro;
use App\Unidad;
use App\User;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use NumberFormatter;
use PDF;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        return view('results.index', $this->resultados($request));
    }

    public function pdf(Request $request)
    {
        $curso = Curso::find(setting_usuario('curso_actual'));
        if (is_null($curso))
            abort(404, __('Course not found.'));

        $pdf = PDF::loadView('results.pdf', $this->resultados($request));

        return $pdf->stream('resultados.pdf');
    }

    private function resultados(Request $request)
    {
        $user = Auth::user();

        if (!empty($request->input('user_id'))) {
            $user_id = $request->input('user_id');
            if ($user_id == -1) {
                session()->forget('filtrar_user_actual');
            } else {
                $user = User::find($user_id);
                session(['filtrar_user_actual' => $user_id]);
            }
        } else if (!empty(session('filtrar_user_actual'))) {
            $user = User::find(session('filtrar_user_actual'));
        }

        // Lista de usuarios
        $curso = Curso::find(setting_usuario('curso_actual'));

        if (!is_null($curso)) {
            $users = $curso->users()->rolAlumno()->noBloqueado()->orderBy('name')->get();
        } else {
            $users = new Collection();
        }

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

                        if (!$actividad->hasEtiqueta('examen')) {
                            $resultados[$skill->id]->puntos_tarea += $puntuacion_tarea;
                            $resultados[$skill->id]->puntos_totales_tarea += $puntuacion_actividad;

                            $resultados[$skill->id]->tarea += $puntuacion_tarea * ($peso_tarea / 100) * ($porcentaje / 100);
                            $resultados[$skill->id]->actividad += $puntuacion_actividad * ($peso_tarea / 100) * ($porcentaje / 100);

                        } else {
                            $resultados[$skill->id]->puntos_examen += $puntuacion_tarea;
                            $resultados[$skill->id]->puntos_totales_examen += $puntuacion_actividad;

                            $resultados[$skill->id]->tarea += $puntuacion_tarea * ($peso_examen / 100) * ($porcentaje / 100);
                            $resultados[$skill->id]->actividad += $puntuacion_actividad * ($peso_examen / 100) * ($porcentaje / 100);
                        }

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

        if ($num_actividades_obligatorias > 0)
            $nota = $nota * ($numero_actividades_completadas / $num_actividades_obligatorias);

        // Formateador con 2 decimales y en el idioma del usuario
        $locale = app()->getLocale();
        $formatStyle = NumberFormatter::DECIMAL;
        $formatter = new NumberFormatter($locale, $formatStyle);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);

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

        $pruebas_evaluacion = true;
        $num_pruebas_evaluacion = 0;
        foreach ($unidades as $unidad) {
            if ($unidad->hasEtiqueta('examen')
                && $user->num_completadas('examen', $unidad->id) > 0
                && $resultados_unidades[$unidad->id]->actividad > 0) {
                $num_pruebas_evaluacion += 1;

                if (($resultados_unidades[$unidad->id]->tarea / $resultados_unidades[$unidad->id]->actividad) * 10 < 5) {
                    $pruebas_evaluacion = false;
                }
            }
        }

        // Total de actividades para el cálculo de la media
        $total_actividades_grupo = 0;
        foreach ($users as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base');
        }

        $media_actividades_grupo = $formatter->format($users->count() > 0 ? $total_actividades_grupo / $users->count() : 0);

        // Gráfico de actividades

        if (!is_null($curso)) {
            $fecha_inicio = $curso->fecha_inicio ?: Carbon::now()->subMonths(3);
            $fecha_fin = $curso->fecha_fin ?: Carbon::now();
        }

        $chart = new TareasEnviadas();

        $registros = [];
        $period = null;

        if (!is_null($curso)) {
            $registros = Registro::where('user_id', $user->id)
                ->where('estado', 30)
                ->whereBetween('timestamp', [$fecha_inicio, $fecha_fin])
                ->whereHas('tarea.actividad.unidad.curso', function ($query) {
                    $query->where('cursos.id', setting_usuario('curso_actual'));
                })->whereHas('tarea.actividad', function ($query) {
                    $query->where('actividades.auto_avance', false);
                })
                ->orderBy('timestamp')
                ->get()
                ->groupBy(function ($val) {
                    return Carbon::parse($val->timestamp)->format('d/m/Y');
                });

            $period = CarbonPeriod::create($fecha_inicio, $fecha_fin);

            $todas_fechas = [];
            foreach ($period as $date) {
                $todas_fechas[$date->format('d/m/Y')] = 0;
            }

            $datos = array_merge($todas_fechas, $registros->map(function ($item, $key) {
                return $item->count();
            })->toArray());

            $chart->labels(array_keys($datos))->displayLegend(false);

            $chart->dataset('Enviadas', 'bar',
                array_values($datos))
                ->color("#3490dc")
                ->backgroundColor("#d6e9f8");
        }

        return compact(['curso', 'skills_curso', 'unidades', 'user', 'users',
            'resultados', 'resultados_unidades', 'nota_final',
            'actividades_obligatorias', 'num_actividades_obligatorias', 'numero_actividades_completadas',
            'pruebas_evaluacion', 'num_pruebas_evaluacion',
            'media_actividades_grupo', 'competencias_50_porciento', 'chart']);
    }
}
