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

        // Obtener las calificaciones del usuario
        $calificaciones = $user->calcular_calificaciones();

        // Calcular la media de actividades del grupo
        $total_actividades_grupo = 0;
        foreach ($users as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base');
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

        return compact(['user', 'curso', 'users', 'unidades', 'calificaciones', 'media_actividades_grupo', 'chart']);
    }
}
