<?php

namespace App\Observers;

use App\Tarea;
use Cache;

class TareaObserver
{
    public function saved(Tarea $tarea)
    {
        Cache::forget('num_actividades_asignadas_total_' . $tarea->user_id);
    }

    public function deleted(Tarea $tarea)
    {
        Cache::forget('num_actividades_asignadas_total_' . $tarea->user_id);
    }
}
