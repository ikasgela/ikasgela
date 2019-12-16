<?php

namespace App\Http\Controllers;

use App\Charts\TareasEnviadas;
use App\Exports\InformeGrupoExport;
use App\Registro;
use App\Tarea;
use App\Traits\InformeGrupo;
use Carbon\Carbon;
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
        memorizar_ruta();

        $this->recuento_enviadas();

        return view('tutor.index', $this->datosInformeGrupo($request));
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

    public function export()
    {
        return Excel::download(new InformeGrupoExport, 'informegrupo.xlsx');
    }

    public function tareas_enviadas()
    {
        $chart = new TareasEnviadas();

        $registros = Registro::where('estado', 30)
            ->whereHas('tarea.actividad.unidad.curso', function ($query) {
                $query->where('cursos.id', setting_usuario('curso_actual'));
            })->whereHas('tarea.actividad', function ($query) {
                $query->where('actividades.auto_avance', false);
            })
            ->orderBy('timestamp')
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->timestamp)->format('Y-m-d');
            });

        $chart->labels($registros->keys()->map(function ($item, $key) {
            return Carbon::parse($item)->format('d/m/Y');
        }))->displayLegend(false);

        $chart->dataset('Enviadas', 'bar',
            $registros->map(function ($item, $key) {
                return $item->count();
            })->values())
            ->color("#3490dc")
            ->backgroundColor("#d6e9f8");

        return view('tutor.tareas_enviadas', ['chart' => $chart]);
    }
}
