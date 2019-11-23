<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InformeGrupoExport implements FromView
{
    public function view(): View
    {
        return view('tutor.export', app('App\Http\Controllers\TutorController')->datosInformeGrupo(request()));
    }
}
