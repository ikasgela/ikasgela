<?php

namespace App\Observers;

use App\Models\Tarea;
use Illuminate\Support\Facades\Cache;

class TareaObserver
{
    use SharedKeys;

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
