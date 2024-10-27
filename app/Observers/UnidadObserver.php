<?php

namespace App\Observers;

use App\Models\Unidad;
use Cache;

class UnidadObserver
{
    public function saved(Unidad $unidad)
    {
        $this->clearCache($unidad);
    }

    public function deleted(Unidad $unidad)
    {
        $this->clearCache($unidad);
    }

    private function clearCache(Unidad $unidad): void
    {
        foreach ($unidad->curso->users()->get() as $user) {
            Cache::forget('calificaciones_' . $user->id);
        }
    }
}
