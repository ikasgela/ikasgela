<?php

namespace App\Traits;

use App\Actividad;
use App\Curso;
use Illuminate\Http\Request;

trait InformeActividadesCurso
{
    public function datosInforme(Request $request, $exportar = false)
    {
        $curso = Curso::find(setting_usuario('curso_actual'));

        $actividades = Actividad::cursoActual()->plantilla()->orderBy('orden')->get();

        return compact(['curso', 'actividades']);
    }
}
