<?php

namespace App\Console;

use App\Actividad;
use Carbon\Carbon;
use GrahamCampbell\GitLab\Facades\GitLab;

class BloquearRepositorios
{
    public function __invoke()
    {
        $actividades = Actividad::where('fecha_limite', '<=', Carbon::now())->get();

        foreach ($actividades as $actividad) {
            foreach ($actividad->intellij_projects as $intellij_project) {
                $proyecto_gitlab = $intellij_project->gitlab();
                if (!$proyecto_gitlab['archived']) {
                    GitLab::projects()->archive($proyecto_gitlab['id']);
                }
            }
        }
    }
}
