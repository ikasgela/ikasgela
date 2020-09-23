<?php

namespace App\Exports;

use App\Traits\InformeActividadesCurso;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ActividadesCursoExport implements FromView
{
    use InformeActividadesCurso;

    public function view(): View
    {
        return view('actividades.export', $this->datosInforme(request(), true));
    }
}
