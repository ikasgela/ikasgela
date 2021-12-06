<?php

namespace App\Http\Controllers;

use App\Charts\TareasEnviadas;
use App\Models\Curso;
use App\Models\Milestone;
use App\Models\Registro;
use App\Models\Unidad;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PDF;

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

        $curso = Curso::find(setting_usuario('curso_actual'));
        if (is_null($curso))
            abort(404, __('Course not found.'));

        $pdf = PDF::loadView('results.pdf', $this->resultados($request));

        return $pdf->stream('resultados.pdf');
    }

    private function resultados(Request $request)
    {
        $user = Auth::user();

        // Hay otro usuario seleccionado para mostrar
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

        // Lista de usuarios para el desplegable
        $curso = Curso::find(setting_usuario('curso_actual'));

        if (!is_null($curso)) {
            $users = $curso->users()->rolAlumno()->noBloqueado()->orderBy('name')->get();
        } else {
            $users = new Collection();
        }

        // Unidades del curso actual
        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        // Evaluaciones del curso actual
        $milestones = $curso->milestones()->orderBy('date')->get();

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

        // Obtener las calificaciones del usuario
        $calificaciones = $user->calcular_calificaciones($milestone);

        // Calcular la media de actividades del grupo
        $total_actividades_grupo = 0;
        foreach ($users as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base', null, $milestone);
        }

        $media_actividades_grupo = formato_decimales($users->count() > 0 ? $total_actividades_grupo / $users->count() : 0, 2);

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

        // Colores de fondo y valores de los resultados de evaluación

        $actividades_obligatorias_fondo = $calificaciones->examen_final || $calificaciones->hay_nota_manual ? 'bg-light text-dark' : ($calificaciones->actividades_obligatorias_superadas ? 'bg-success text-dark' : 'bg-warning text-dark');
        $actividades_obligatorias_dato = $calificaciones->num_actividades_obligatorias > 0 ? $calificaciones->actividades_obligatorias_superadas ? trans_choice('tasks.completed', 2) : ($calificaciones->numero_actividades_completadas + 0) . "/" . ($calificaciones->num_actividades_obligatorias + 0) : __('None');

        $competencias_fondo = $calificaciones->examen_final || $calificaciones->hay_nota_manual ? 'bg-light text-dark' : ($calificaciones->competencias_50_porciento ? 'bg-success text-dark' : 'bg-warning text-dark');
        $competencias_dato = $calificaciones->competencias_50_porciento ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2);

        $pruebas_evaluacion_fondo = ($curso?->examenes_obligatorios || $calificaciones->examen_final) && !$calificaciones->hay_nota_manual
            ? ($calificaciones->examen_final ? ($calificaciones->examen_final_superado ? 'bg-success text-dark' : 'bg-warning text-dark')
                : ($calificaciones->pruebas_evaluacion ? 'bg-success text-dark' : 'bg-warning text-dark')) : 'bg-light text-dark';
        $pruebas_evaluacion_dato = ($calificaciones->num_pruebas_evaluacion > 0 || $curso?->examenes_obligatorios || $calificaciones->examen_final)
            ? ($calificaciones->examen_final ? ($calificaciones->examen_final_superado ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2))
                : ($calificaciones->pruebas_evaluacion ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2))) : __('None');

        $evaluacion_continua_fondo = $calificaciones->examen_final || $calificaciones->hay_nota_manual ? 'bg-light text-dark' : ($calificaciones->evaluacion_continua_superada ? 'bg-success text-dark' : 'bg-warning text-dark');
        $evaluacion_continua_dato = $calificaciones->evaluacion_continua_superada ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1);

        $calificacion_fondo = ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? 'bg-success text-dark' : ($curso?->disponible() ? 'bg-light text-dark' : 'bg-warning text-dark');
        $calificacion_dato = ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? $calificaciones->nota_final : ($curso?->disponible() ? __('Unavailable') : __('Fail'));

        return compact(['user', 'curso', 'users', 'unidades', 'calificaciones', 'media_actividades_grupo', 'chart',
            'actividades_obligatorias_fondo', 'actividades_obligatorias_dato',
            'competencias_fondo', 'competencias_dato',
            'pruebas_evaluacion_fondo', 'pruebas_evaluacion_dato',
            'evaluacion_continua_fondo', 'evaluacion_continua_dato',
            'calificacion_fondo', 'calificacion_dato',
            'milestones', 'milestone'
        ]);
    }
}
