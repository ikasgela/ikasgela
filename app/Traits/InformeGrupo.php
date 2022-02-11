<?php

namespace App\Traits;

use App\Models\Curso;
use App\Models\Milestone;
use App\Models\Organization;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
                $usuarios = $curso?->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get()->sortBy('num_completadas_base') ?? new Collection();
                break;
            default:
                $usuarios = $curso?->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get() ?? new Collection();
                break;
        }

        $unidades = Unidad::cursoActual()->orderBy('orden')->get();

        // Evaluaciones del curso actual
        $milestones = $curso?->milestones()->orderBy('date')->get() ?? [];

        // Hay otra evaluación seleccionada para mostrar
        $milestone = null;
        if (!empty($request->input('milestone_id'))) {
            $milestone_id = $request->input('milestone_id');
            if ($milestone_id == -1) {
                session()->forget('filtrar_milestone_actual');
            } else {
                $milestone = Milestone::find($milestone_id);
                session(['filtrar_milestone_actual' => $milestone_id]);
            }
        } else if (!empty(session('filtrar_milestone_actual'))) {
            $milestone = Milestone::find(session('filtrar_milestone_actual'));
        }

        // Total de actividades para el cálculo de la media
        $total_actividades_grupo = 0;
        foreach ($usuarios as $usuario) {
            $total_actividades_grupo += $usuario->num_completadas('base', null, $milestone);
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
            'media_actividades_grupo', 'media_actividades_grupo_formato',
            'milestones', 'milestone'
        ]);
    }
}
