<?php

namespace App\Livewire;

use App\Models\IntellijProject;
use Livewire\Component;

class TarjetaIntellij extends Component
{
    public $intellij_project_id;
    public $intellij_project;
    public $actividad;
    public $repositorio;

    public function mount()
    {
        $this->intellij_project = IntellijProject::findOrFail($this->intellij_project_id);
        $this->actividad = $this->intellij_project->actividades()->first();
        $this->repositorio = $this->intellij_project->repository();
    }

    public function render()
    {
        return view('livewire.tarjeta-intellij')
            ->extends('layouts.app');
    }
}
