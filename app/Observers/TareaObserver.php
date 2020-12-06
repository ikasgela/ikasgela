<?php

namespace App\Observers;

use App\Tarea;
use Cache;

class TareaObserver
{
    protected $keys = [
        'num_actividades_nuevas_',
        'num_actividades_ocultas_',
        'num_actividades_aceptadas_',
        'num_actividades_caducadas_',
        'num_actividades_en_curso_',
        'num_actividades_en_curso_autoavance_',
        'num_actividades_enviadas_noautoavance_',
        'num_actividades_revisadas_',
        'num_actividades_archivadas_',
        'num_actividades_completadas_',
        'num_actividades_sin_completar_',
        'num_actividades_asignadas_total_',
        'siguiente_actividad_',
    ];

    public function saved(Tarea $tarea)
    {
        $this->clearCache($tarea);
    }

    public function deleted(Tarea $tarea)
    {
        $this->clearCache($tarea);
    }

    private function clearCache(Tarea $tarea): void
    {
        foreach ($this->keys as $key) {
            Cache::forget($key . $tarea->user_id);
        }
    }
}
