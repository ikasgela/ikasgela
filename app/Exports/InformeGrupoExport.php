<?php

namespace App\Exports;

use App\Traits\InformeGrupo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InformeGrupoExport implements FromView
{
    use InformeGrupo;

    public function view(): View
    {
        return view('tutor.export', $this->datosInformeGrupo(request()));
    }
}
