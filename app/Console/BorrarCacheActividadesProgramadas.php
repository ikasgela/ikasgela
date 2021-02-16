<?php

namespace App\Console;

use App\Models\CacheClear;
use Log;

class BorrarCacheActividadesProgramadas
{
    public function __invoke()
    {
        $pendientes = CacheClear::where('fecha', '=', now())->get();

        $total = 0;

        foreach ($pendientes as $pendiente) {
            $pendiente->user->clearCache();
            $total += 1;
        }

        Log::info('Cachés borradas.', [
            'total' => $total,
        ]);
    }
}
