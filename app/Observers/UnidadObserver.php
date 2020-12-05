<?php

namespace App\Observers;

use App\Unidad;

class UnidadObserver
{
    public function saved(Unidad $unidad)
    {
        Unidad::flushCache();
    }

    public function deleted(Unidad $unidad)
    {
        Unidad::flushCache();
    }
}
