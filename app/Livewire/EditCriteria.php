<?php

namespace App\Livewire;

use App\Models\Criteria;
use Livewire\Component;

class EditCriteria extends Component
{
    public Criteria $criteria;

    public $texto;
    public $puntuacion;

    public function mount($criteria_id)
    {
        $this->criteria = Criteria::findOrFail($criteria_id);
        $this->texto = $this->criteria->texto;
        $this->puntuacion = $this->criteria->puntuacion;
    }

    public function render()
    {
        return view('livewire.edit-criteria');
    }

    public function save()
    {
        $this->criteria->texto = $this->texto;
        $this->criteria->puntuacion = $this->puntuacion;
        $this->criteria->save();

        $this->dispatch('hideModal');
        $this->dispatch('$parent.$refresh');
    }
}
