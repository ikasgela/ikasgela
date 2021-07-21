<?php

namespace App\Console;

use App\Models\Actividad;
use Log;

class BloquearRepositorios
{
    public function __invoke()
    {
        $actividades = Actividad::where('plantilla', false)
            ->where('fecha_limite', '<=', now())->with('intellij_projects')->get();

        $total = 0;
        $archivados = 0;

        foreach ($actividades->flatMap->intellij_projects as $intellij_project) {

            if ($intellij_project->isForked() && !$intellij_project->isArchivado()) {
                $intellij_project->archive();
                $archivados += 1;
            }
            $total += 1;
        }

        Log::info('Repositorios archivados.', [
            'total' => $total,
            'archivados' => $archivados,
        ]);
    }
}
