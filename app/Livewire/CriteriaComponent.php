<?php

namespace App\Livewire;

use App\Models\Criteria;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CriteriaComponent extends Component
{
    #[Reactive]
    public Criteria $criteria;

    #[Reactive]
    public $rubric_is_editing = false;
    public $criteria_is_editing = false;

    public $texto;
    public $puntuacion;

    #[Reactive]
    public $total = 0;

    public function render()
    {
        return view('livewire.criteria');
    }

    public function toggle_edit()
    {
        $this->criteria_is_editing = !$this->criteria_is_editing;
    }

    public function save()
    {
        $this->criteria->texto = $this->texto;
        $this->criteria->puntuacion = $this->puntuacion;
        $this->criteria->save();

        $this->criteria_is_editing = false;
    }
}
