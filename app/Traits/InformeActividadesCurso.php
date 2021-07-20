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

        if (session('profesor_unidad_id_disponibles')) {
            $actividades = Actividad::cursoActual()->plantilla()->where('unidad_id', session('profesor_unidad_id_disponibles'))->orderBy('orden')->orderBy('id')->get();
        } else {
            $actividades = Actividad::cursoActual()->plantilla()->orderBy('orden')->orderBy('id')->get();
        }

        return compact(['curso', 'actividades']);
    }
}
