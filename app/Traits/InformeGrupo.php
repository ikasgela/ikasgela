<?php

namespace App\Traits;

use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait InformeGrupo
{
    public function datosInformeGrupo(Request $request, $exportar = false)
    {
        $user = Auth::user();
        $organization = $user->organizacion_actual();
        $curso = $user->curso_actual();

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

        $unidades = $curso->unidades()->whereVisible(true)->orderBy('orden')->get();

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

        // Actividades obligatorias

        $num_actividades_obligatorias = 0;
        foreach ($unidades as $unidad) {
            if ($unidad->num_actividades('base') > 0) {
                $num_actividades_obligatorias += $unidad->num_actividades('base');
            }
        }

        $media_actividades_grupo = $curso?->media($milestone);
        $media_actividades_grupo_formato = formato_decimales($media_actividades_grupo, 2, $exportar);

        $mediana = $curso?->mediana($milestone);

        return compact(['usuarios', 'unidades', 'organization',
            'curso', 'num_actividades_obligatorias',
            'media_actividades_grupo', 'media_actividades_grupo_formato',
            'milestones', 'milestone',
            'mediana',
        ]);
    }
}
