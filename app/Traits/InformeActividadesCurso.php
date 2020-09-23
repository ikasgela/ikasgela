<?php

namespace App\Traits;

use App\Curso;
use Illuminate\Http\Request;

trait InformeActividadesCurso
{
    public function datosInforme(Request $request, $exportar = false)
    {
        $curso = Curso::find(setting_usuario('curso_actual'));

        $actividades = $curso->actividades()->get();

        return compact(['curso', 'actividades']);
    }
}
