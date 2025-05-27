<?php

namespace App\Http\Controllers;

use App\Charts\TareasEnviadas;
use App\Models\Milestone;
use App\Models\Registro;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        return view('results.index', $this->resultados($request));
    }

    public function pdf(Request $request)
    {
        $user = Auth::user();

        if ($user->baja_ansiedad) {
            abort(404);
        }

        $curso = $user->curso_actual();
        if (is_null($curso))
            abort(404, __('Course not found.'));

        $pdf = PDF::loadView('results.pdf', $this->resultados($request, true));

        return $pdf->stream('resultados.pdf');
    }

    private function resultados(Request $request, $pdf = false)
    {
        $user = Auth::user();

        // Hay otro usuario seleccionado para mostrar
        if (Auth::user()->hasAnyRole(['admin', 'profesor', 'tutor'])) {
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
        }

        // Lista de usuarios para el desplegable
        $curso = $user->curso_actual();

        if (!is_null($curso)) {
            $users = $curso->alumnos_activos();
        } else {
            $users = new Collection();
        }

        // Unidades del curso actual
        $unidades = $curso?->unidades()->whereVisible(true)->orderBy('orden')->get() ?? new Collection();

        // Evaluaciones del curso actual
        if (Auth::user()->hasAnyRole(['admin', 'profesor', 'tutor'])) {
            $milestones = $curso?->milestones()->orderBy('date')->get() ?? new Collection();
        } else {
            $milestones = $curso?->milestones()->published()->orderBy('date')->get() ?? new Collection();
        }

        // Hay otra evaluación seleccionada para mostrar
        $milestone = null;
        if (!empty($request->input('milestone_id'))) {
            $milestone_id = $request->input('milestone_id');
            if ($milestone_id == -1) {
                session()->forget('filtrar_milestone_actual');
            } else {
                $milestone = Milestone::find($milestone_id);
                session(['filtrar_milestone_actual' => $milestone_id]);
            }
        } else if (!empty(session('filtrar_milestone_actual'))) {
            $milestone = Milestone::find(session('filtrar_milestone_actual'));
        }

        // Calcular la media y la mediana de actividades del grupo
        $media = $curso?->media($milestone);
        $mediana = $curso?->mediana($milestone);
        $media_actividades_grupo = formato_decimales($media, 2);
        $mediana_actividades_grupo = formato_decimales($mediana, 2);

        // Ajuste proporcional de la nota según las actividades completadas
        $ajuste_proporcional_nota = $milestone?->ajuste_proporcional_nota ?: $curso?->ajuste_proporcional_nota;
        $calificaciones = match ($ajuste_proporcional_nota) {
            'media' => $user->calcular_calificaciones($media, $milestone),
            'mediana' => $user->calcular_calificaciones($mediana, $milestone),
            default => $user->calcular_calificaciones(0, $milestone),
        };

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
                ->groupBy(fn($val) => Carbon::parse($val->timestamp)->isoFormat('L'));

            $period = CarbonPeriod::create($fecha_inicio, $fecha_fin);

            $todas_fechas = [];
            foreach ($period as $date) {
                $todas_fechas[$date->isoFormat('L')] = 0;
            }

            $datos = array_merge($todas_fechas, $registros->map(fn($item, $key) => $item->count())->toArray());

            $chart->labels(array_keys($datos))->displayLegend(false);

            $chart->dataset(trans_choice('tasks.sent', 2), 'bar',
                array_values($datos))
                ->color("#3490dc")
                ->backgroundColor("#d6e9f8");
        }

        // Colores de fondo y valores de los resultados de evaluación

        if (!$pdf) {
            $bg_light = 'text-bg-light';
            $bg_success = 'text-bg-success';
            $bg_warning = 'text-bg-warning';
        } else {
            $bg_light = 'bg-light text-dark';
            $bg_success = 'bg-success text-dark';
            $bg_warning = 'bg-warning text-dark';
        }

        $actividades_obligatorias_fondo = $calificaciones->examen_final || $calificaciones->hay_nota_manual ? $bg_light : ($calificaciones->actividades_obligatorias_superadas ? $bg_success : $bg_warning);
        $actividades_obligatorias_dato = $calificaciones->num_actividades_obligatorias > 0 ? $calificaciones->actividades_obligatorias_superadas ? trans_choice('tasks.completed', 2) : ($calificaciones->numero_actividades_completadas + 0) . "/" . ($calificaciones->num_actividades_obligatorias + 0) : __('None');

        $competencias_fondo = $calificaciones->examen_final || $calificaciones->hay_nota_manual ? $bg_light : ($calificaciones->competencias_50_porciento ? $bg_success : $bg_warning);
        $competencias_dato = $calificaciones->competencias_50_porciento ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2);

        $pruebas_evaluacion_fondo = ($curso?->examenes_obligatorios || $calificaciones->examen_final) && !$calificaciones->hay_nota_manual
            ? ($calificaciones->examen_final ? ($calificaciones->examen_final_superado ? $bg_success : $bg_warning)
                : ($calificaciones->pruebas_evaluacion ? $bg_success : $bg_warning)) : $bg_light;
        $pruebas_evaluacion_dato = ($calificaciones->num_pruebas_evaluacion > 0 || $curso?->examenes_obligatorios || $calificaciones->examen_final)
            ? ($calificaciones->examen_final ? ($calificaciones->examen_final_superado ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2))
                : ($calificaciones->pruebas_evaluacion ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2))) : __('None');

        $evaluacion_continua_fondo = $calificaciones->examen_final || $calificaciones->hay_nota_manual ? $bg_light : ($calificaciones->evaluacion_continua_superada ? $bg_success : $bg_warning);
        $evaluacion_continua_dato = $calificaciones->evaluacion_continua_superada ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1);

        $usuarios = $curso?->alumnos_activos() ?? [];

        // Calcular el rango de nota máxima y mínima para normalizar
        $rango = null;
        if ($curso?->normalizar_nota || $milestone?->normalizar_nota) {
            $todas_notas = [];
            foreach ($usuarios as $usuario) {
                $todas_notas[] = match ($ajuste_proporcional_nota) {
                    'media' => $usuario->calcular_calificaciones($media, $milestone)->nota_numerica,
                    'mediana' => $usuario->calcular_calificaciones($mediana, $milestone)->nota_numerica,
                    default => $usuario->calcular_calificaciones(0, $milestone)->nota_numerica,
                };
            }
            $nota_maxima = count($todas_notas) > 0 ? max($todas_notas) : 0;
            $nota_minima = count($todas_notas) > 0 ? min($todas_notas) : 0;

            // Si el curso o la evaluación lo activan, normalizar la nota entre min y max
            $rango = ($curso?->normalizar_nota || $milestone?->normalizar_nota) ? ['min' => $nota_minima, 'max' => $nota_maxima] : null;
        }

        $calificacion_fondo = ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? $bg_success : ($curso?->disponible() ? $bg_light : $bg_warning);
        $calificacion_dato = ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada || $milestone != null) ? $calificaciones->nota_final($rango) : ($curso?->disponible() ? __('Unavailable') : __('Fail'));
        $calificacion_dato_publicar = ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada || $milestone != null) ? $calificaciones->nota_publicar($milestone, $rango) : ($curso?->disponible() ? __('Unavailable') : __('Fail'));

        return compact(['user', 'curso', 'users', 'unidades', 'calificaciones', 'chart',
            'actividades_obligatorias_fondo', 'actividades_obligatorias_dato',
            'competencias_fondo', 'competencias_dato',
            'pruebas_evaluacion_fondo', 'pruebas_evaluacion_dato',
            'evaluacion_continua_fondo', 'evaluacion_continua_dato',
            'calificacion_fondo', 'calificacion_dato', 'calificacion_dato_publicar',
            'milestones', 'milestone',
            'media', 'media_actividades_grupo', 'mediana', 'mediana_actividades_grupo',
        ]);
    }
}
