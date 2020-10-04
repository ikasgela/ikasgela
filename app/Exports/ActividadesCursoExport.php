<?php

namespace App\Exports;

use App\Traits\InformeActividadesCurso;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActividadesCursoExport implements FromView, ShouldAutoSize, WithStyles
{
    use InformeActividadesCurso;

    public function view(): View
    {
        return view('actividades.export', $this->datosInforme(request(), true));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true]],
        ];
    }
}
