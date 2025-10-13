<?php

namespace App\Observers;

use App\Models\Tarea;
use Illuminate\Support\Facades\Cache;

class TareaObserver
{
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
        Cache::tags('user_' . $tarea->user_id)->flush();
        Cache::tags('curso_' . $tarea->actividad->unidad->curso->id)->forget('recuento_enviadas');
    }
}
