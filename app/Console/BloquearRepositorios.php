<?php

namespace App\Console;

use App\Actividad;
use Log;

class BloquearRepositorios
{
    public function __invoke()
    {
        $actividades = Actividad::where('plantilla', false)
            ->where('is_expired', true)->get();

        $total = 0;
        $archivados = 0;

        foreach ($actividades as $actividad) {
            foreach ($actividad->intellij_projects as $intellij_project) {

                if (!$intellij_project->isArchivado() && $intellij_project->isForked()) {
                    $intellij_project->archive();
                    $archivados += 1;
                }
                $total += 1;
            }
        }

        Log::info('Repositorios archivados.', [
            'total' => $total,
            'archivados' => $archivados,
        ]);
    }
}
