<?php

namespace App\Console;

use App\Actividad;
use Carbon\Carbon;
use GrahamCampbell\GitLab\Facades\GitLab;

class BloquearRepositorios
{
    public function __invoke()
    {
        $actividades = Actividad::where('plantilla', false)->where('fecha_limite', '<=', Carbon::now())->get();

        $total = 0;

        foreach ($actividades as $actividad) {
            foreach ($actividad->intellij_projects as $intellij_project) {
                if (!$intellij_project->archivado) {
                    $proyecto_gitlab = $intellij_project->gitlab();
                    if (!$proyecto_gitlab['archived']) {
                        GitLab::projects()->archive($proyecto_gitlab['id']);
                        $intellij_project->archivado = true;
                        $intellij_project->save();
                        $total += 1;
                    }
                }
            }
        }

        echo 'Repositorios archivados: ' . $total;
    }
}
