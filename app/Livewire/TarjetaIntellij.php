<?php

namespace App\Livewire;

use App\Jobs\ForkGiteaRepo;
use App\Models\Actividad;
use App\Models\IntellijProject;
use App\Models\Tarea;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class TarjetaIntellij extends Component
{
    public ?Actividad $actividad;
    public IntellijProject $intellij_project;
    public $repositorio;
    public $fork_status;    // 0 sin clonar, 1 clonando, 2 completado, 3 error

    public function mount(IntellijProject $intellij_project)
    {
        $this->intellij_project = $intellij_project;
        $this->fork_status = $this->intellij_project->getForkStatus();
        $this->repositorio = $this->intellij_project->repository();
    }

    public function render()
    {
        return view('livewire.tarjeta-intellij');
    }

    public function fork()
    {
        if (!$this->intellij_project->isForking()) {
            $this->fork_status = 1;

            if (!App::environment('testing')) {
                $team_users = [];
                if ($this->actividad->hasEtiqueta('trabajo en equipo')) {
                    $compartidas = Tarea::where('actividad_id', $this->actividad->id)->get();
                    foreach ($compartidas as $compartida) {
                        array_push($team_users, $compartida->user);
                    }
                }
                ForkGiteaRepo::dispatch($this->actividad, $this->intellij_project, Auth::user(), $team_users);
            } else {
                ForkGiteaRepo::dispatchSync($this->actividad, $this->intellij_project, Auth::user());
            }
        }
    }

    #[On('echo:forks.{intellij_project.id},GiteaRepoForked')]
    public function forked($event)
    {
        $this->intellij_project = $this->actividad->intellij_projects()->find($event['intellij_project']['id']);
        $this->fork_status = $this->intellij_project->getForkStatus();
        $this->repositorio = $this->intellij_project->repository();
        Log::debug("Fork del repositorio completado", $event);
    }
}
