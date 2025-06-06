<?php

namespace App\Livewire;

use App\Jobs\ForkGiteaRepo;
use App\Models\Actividad;
use App\Models\IntellijProject;
use App\Models\Tarea;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TarjetaIntellij extends Component
{
    public $actividad;
    public $intellij_project;
    public $repositorio;
    public $fork_status;

    public function mount(Actividad $actividad, IntellijProject $intellij_project)
    {
        $this->intellij_project = $intellij_project;
        $this->actividad = $actividad;
        $this->repositorio = $this->intellij_project->repository();
        $this->fork_status = $intellij_project->getForkStatus();
    }

    public function render()
    {
        return view('livewire.tarjeta-intellij');
//            ->extends('layouts.app');
    }

    public function prueba()
    {
        $this->fork($this->actividad, $this->intellij_project);
    }

    public function fork(Actividad $actividad, IntellijProject $intellij_project)
    {
        $proyecto = $intellij_project;

        if (!$proyecto->isForking()) {
            $this->fork_status = 1; // Forking

            if (!App::environment('testing')) {
                $team_users = [];
                if ($actividad->hasEtiqueta('trabajo en equipo')) {
                    $compartidas = Tarea::where('actividad_id', $actividad->id)->get();
                    foreach ($compartidas as $compartida) {
                        array_push($team_users, $compartida->user);
                    }
                }
                ForkGiteaRepo::dispatchSync($actividad, $intellij_project, Auth::user());

//                ForkGiteaRepo::dispatch($actividad, $intellij_project, Auth::user(), $team_users);
            } else {
                ForkGiteaRepo::dispatchSync($actividad, $intellij_project, Auth::user());
            }

            $this->fork_status = 2; // Forked
        }
    }
}
