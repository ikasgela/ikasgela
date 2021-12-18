<?php

namespace App\Http\Controllers;

use App\Charts\TareasEnviadas;
use App\Exports\InformeGrupoExport;
use App\Models\Curso;
use App\Models\Registro;
use App\Models\Tarea;
use App\Traits\InformeGrupo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TutorController extends Controller
{
    use InformeGrupo;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->recuento_enviadas();

        return view('tutor.index', $this->datosInformeGrupo($request));
    }

    private function recuento_enviadas(): void
    {
        $tareas = Tarea::cursoActual()->usuarioNoBloqueado()->noAutoAvance()->where('estado', 30)->get();

        $num_enviadas = count($tareas);
        if ($num_enviadas > 0)
            session(['num_enviadas' => $num_enviadas]);
        else
            session()->forget('num_enviadas');
    }

    public function export()
    {
        return Excel::download(new InformeGrupoExport, 'informegrupo.xlsx');
    }

    public function tareas_enviadas()
    {
        $curso = Curso::find(setting_usuario('curso_actual'));

        $fecha_inicio = $curso?->fecha_inicio ?: Carbon::now()->subMonths(3);
        $fecha_fin = $curso?->fecha_fin ?: Carbon::now();

        $chart = new TareasEnviadas();

        $registros = Registro::where('estado', 30)
            ->whereBetween('timestamp', [$fecha_inicio, $fecha_fin])
            ->whereHas('tarea.actividad.unidad.curso', function ($query) {
                $query->where('cursos.id', setting_usuario('curso_actual'));
            })->whereHas('tarea.actividad', function ($query) {
                $query->where('actividades.auto_avance', false);
            })
            ->orderBy('timestamp')
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->timestamp)->isoFormat('L');
            });

        $period = CarbonPeriod::create($fecha_inicio, $fecha_fin);

        $todas_fechas = [];
        foreach ($period as $date) {
            $todas_fechas[$date->isoFormat('L')] = 0;
        }

        $datos = array_merge($todas_fechas, $registros->map(function ($item, $key) {
            return $item->count();
        })->toArray());

        $chart->labels(array_keys($datos))->displayLegend(false);

        $chart->dataset(trans_choice('tasks.sent', 2), 'bar',
            array_values($datos))
            ->color("#3490dc")
            ->backgroundColor("#d6e9f8");

        return view('tutor.tareas_enviadas', compact(['chart', 'curso']));
    }
}
