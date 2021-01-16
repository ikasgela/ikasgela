<?php

namespace App\Traits;

use App\Curso;
use App\Organization;
use App\Unidad;
use Illuminate\Http\Request;

trait InformeGrupo
{
    public function datosInformeGrupo(Request $request, $exportar = false)
    {
        $organization = Organization::find(setting_usuario('_organization_id'));

        $curso = Curso::find(setting_usuario('curso_actual'));

        if ($request->has('filtro_alumnos')) {
            session(['tutor_filtro_alumnos' => $request->input('filtro_alumnos')]);
        }

        switch (session('tutor_filtro_alumnos')) {
            case 'P':
                $usuarios = $curso->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get()->sortBy('num_completadas_base');
                break;
            default:
                $usuarios = $curso->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get();
                break;
        }

        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        // Total de actividades para el cálculo de la media
        $total_actividades_grupo = 0;
        foreach ($usuarios as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base');
        }

        // Actividades obligatorias

        $num_actividades_obligatorias = 0;
        foreach ($unidades as $unidad) {
            if ($unidad->num_actividades('base') > 0) {
                $num_actividades_obligatorias += $unidad->num_actividades('base');
            }
        }

        $media_actividades_grupo = $usuarios->count() > 0 ? $total_actividades_grupo / $usuarios->count() : 0;
        $media_actividades_grupo_formato = formato_decimales($media_actividades_grupo, 2, $exportar);

        return compact(['usuarios', 'unidades', 'organization',
            'total_actividades_grupo', 'curso', 'num_actividades_obligatorias',
            'media_actividades_grupo', 'media_actividades_grupo_formato']);
    }
}
