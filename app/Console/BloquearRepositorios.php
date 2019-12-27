<?php

namespace App\Console;

use App\Actividad;
use Carbon\Carbon;
use GitLab;
use Log;

class BloquearRepositorios
{
    public function __invoke()
    {
        $actividades = Actividad::where('plantilla', false)
            ->where('fecha_limite', '<=', Carbon::now())->get();

        $total = 0;

        foreach ($actividades as $actividad) {
            foreach ($actividad->intellij_projects as $intellij_project) {
                if (!$intellij_project->isArchivado()) {
                    $proyecto_gitlab = $intellij_project->gitlab();
                    if (!$proyecto_gitlab['archived']) {
                        GitLab::projects()->archive($proyecto_gitlab['id']);
                        $actividad->intellij_projects()
                            ->updateExistingPivot($intellij_project->id, ['archivado' => true]);
                        $total += 1;
                    }
                }
            }
        }

        Log::debug("Repositorios archivados: $total");
    }
}
