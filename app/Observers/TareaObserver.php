<?php

namespace App\Observers;

use App\Models\Actividad;
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
        // The user-level cache repopulates by calling ->count() on Actividad queries via
        // CacheableBuilder, which has its own model-level cache. We must flush that too,
        // otherwise the repopulation reads a stale cached count from the model cache.
        Actividad::flushModelCache();
        Cache::tags('curso_' . $tarea->actividad->unidad->curso->id)->forget('recuento_enviadas');
    }
}
