<?php

namespace App\Http\Controllers;

use App\Exports\InformeGrupoExport;
use App\Tarea;
use App\Traits\InformeGrupo;
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
}
