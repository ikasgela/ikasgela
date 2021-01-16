<?php

namespace App\Observers;

use App\Unidad;
use Cache;

class UnidadObserver
{
    public function saved(Unidad $unidad)
    {
        Unidad::flushCache();
        $this->clearCache($unidad);
    }

    public function deleted(Unidad $unidad)
    {
        Unidad::flushCache();
        $this->clearCache($unidad);
    }

    private function clearCache(Unidad $unidad): void
    {
        foreach ($unidad->curso->users()->get() as $user) {
            Cache::forget('calificaciones_' . $user->id);
        }
    }
}
