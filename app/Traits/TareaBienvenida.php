<?php

namespace App\Traits;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\User;

trait TareaBienvenida
{
    public function asignarTareaBienvenida(Curso $curso, User $user): void
    {
        if ($user->actividades()->count() == 0) {
            if (isset($curso->tarea_bienvenida_id)) {
                $actividad = Actividad::find($curso->tarea_bienvenida_id);
            } else {
                $actividad = Actividad::whereHas('unidad.curso', function ($query) use ($curso) {
                    $query->where('id', $curso->id);
                })->where('slug', 'tarea-de-bienvenida')
                    ->where('plantilla', true)
                    ->first();
            }

            if (isset($actividad)) {
                $clon = $actividad->duplicate();
                $clon->plantilla_id = $actividad->id;
                $clon->save();
                $user->actividades()->attach($clon, ['puntuacion' => $actividad->puntuacion]);
            }
        }
    }
}
