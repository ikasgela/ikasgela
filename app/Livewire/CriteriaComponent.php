<?php

namespace App\Livewire;

use App\Models\Criteria;
use Livewire\Component;

class CriteriaComponent extends Component
{
    public Criteria $criteria;

    public $rubric_edit_mode = false;
    public $is_editing = false;

    public $texto;
    public $puntuacion;

    public $total = 0;

    public function mount(Criteria $criteria)
    {
        $this->criteria = $criteria;

        $this->texto = $criteria->texto;
        $this->puntuacion = $criteria->puntuacion;
    }

    public function render()
    {
        return view('livewire.criteria');
    }

    public function toggle_edit()
    {
        $this->is_editing = !$this->is_editing;
    }

    public function save()
    {
        $this->criteria->texto = $this->texto;
        $this->criteria->puntuacion = $this->puntuacion;
        $this->criteria->save();

        $this->is_editing = false;
    }
}
