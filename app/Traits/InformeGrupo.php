<?php

namespace App\Traits;

use App\Models\Milestone;
use App\Models\User;
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

        if ($request->has('filtro_alumnos')) {
            session(['tutor_filtro_alumnos' => $request->input('filtro_alumnos')]);
        }

        // Seleccionar el ajuste de nota a aplicar
        $ajuste_proporcional_nota = $milestone?->ajuste_proporcional_nota ?: $curso?->ajuste_proporcional_nota;
        $mediana = match ($ajuste_proporcional_nota) {
            'media' => $curso?->media($milestone),
            'mediana' => $curso?->mediana($milestone),
            default => 0,
        };

        $usuarios = match (session('tutor_filtro_alumnos')) {
            'P' => $curso?->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get()
                ->sortBy(fn($user) => $user->num_completadas('base', null, $milestone) . $user->calcular_calificaciones($mediana, $milestone)->nota_numerica) ?? new Collection(),
            default => $curso?->users()->rolAlumno()->noBloqueado()->orderBy('surname')->orderBy('name')->get() ?? new Collection(),
        };

        // Mostrar o no los nombres de los alumnos
        if ($request->has('informe_anonimo')) {
            if (session('tutor_informe_anonimo') == 'A') {
                session(['tutor_informe_anonimo' => '']);
            } else {
                session(['tutor_informe_anonimo' => $request->input('informe_anonimo')]);
            }
        }

        // Lista de usuarios para el desplegable
        if (!is_null($curso)) {
            $users = $curso->alumnos_activos();
        } else {
            $users = new Collection();
        }

        // Hay otro usuario seleccionado para mostrar
        if (Auth::user()->hasAnyRole(['admin', 'profesor', 'tutor'])) {
            if (!empty($request->input('user_id'))) {
                $user_id = $request->input('user_id');
                if ($user_id == -1) {
                    session()->forget('filtrar_user_actual');
                } else {
                    $user = User::find($user_id);
                    session(['filtrar_user_actual' => $user_id]);
                }
            } else if (!empty(session('filtrar_user_actual'))) {
                $user = User::find(session('filtrar_user_actual'));
            }
        }

        $user_seleccionado = $user;

        // Calcular la nota máxima y mínima para normalizar
        $todas_notas = [];
        foreach ($usuarios as $usuario) {
            $todas_notas[] = $usuario->calcular_calificaciones($mediana, $milestone)->nota_numerica;
        }
        $nota_maxima = count($todas_notas) > 0 ? max($todas_notas) : 0;
        $nota_minima = count($todas_notas) > 0 ? min($todas_notas) : 0;

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
        $mediana_formato = formato_decimales($mediana, 2, $exportar);

        return compact(['usuarios', 'unidades', 'organization',
            'curso', 'num_actividades_obligatorias',
            'media_actividades_grupo', 'media_actividades_grupo_formato',
            'milestones', 'milestone',
            'mediana', 'mediana_formato',
            'nota_maxima', 'nota_minima',
            'users', 'user_seleccionado'
        ]);
    }
}
