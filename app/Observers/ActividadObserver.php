<?php

namespace App\Observers;

use App\Models\Actividad;
use App\Models\Tarea;
use Cache;

class ActividadObserver
{
    use SharedKeys;

    public function saved(Actividad $actividad)
    {
        $this->clearCache($actividad);
    }

    public function deleted(Actividad $actividad)
    {
        $this->clearCache($actividad);
    }

    private function clearCache(Actividad $actividad): void
    {
        $tareas = Tarea::where('actividad_id', $actividad->id)->get();

        foreach ($tareas as $tarea) {
            foreach ($this->keys as $key) {
                Cache::forget($key . $tarea->user_id);
            }
        }
    }
}
