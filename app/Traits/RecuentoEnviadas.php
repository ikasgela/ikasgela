<?php

namespace App\Traits;

use App\Models\Tarea;

trait RecuentoEnviadas
{
    private function recuento_enviadas(): void
    {
        $tareas = Tarea::cursoActual()->usuarioNoBloqueado()->noAutoAvance()->where('estado', 30)->get();

        $num_enviadas = count($tareas);
        if ($num_enviadas > 0)
            session(['num_enviadas' => $num_enviadas]);
        else
            session()->forget('num_enviadas');
    }
}
