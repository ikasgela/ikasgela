<?php

namespace App\Livewire;

use App\Models\Actividad;
use App\Models\Tarea;
use Livewire\Component;

class BotonAccion extends Component
{
    public Actividad $actividad;
    public Tarea $tarea;

    public function mount(Actividad $actividad, Tarea $tarea)
    {
        $this->actividad = $actividad;
        $this->tarea = $tarea;
    }

    public function render()
    {
        return view('livewire.boton-accion');
    }

    protected $listeners = [
        'status-updated.{actividad.id}' => '$refresh'
    ];
}
