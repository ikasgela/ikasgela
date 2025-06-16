<?php

namespace App\Livewire;

use App\Models\Criteria;
use App\Models\Rubric;
use Livewire\Component;

class RubricShow extends Component
{
    public Rubric $rubric;

    public function mount(Rubric $rubric)
    {
        $this->rubric = $rubric;
    }

    public function seleccionar($id)
    {
        $criteria = Criteria::findOrFail($id);
        $criteria->seleccionado = !$criteria->seleccionado;
        $criteria->save();

        $criteria_group = $criteria->criteria_group;
        foreach ($criteria_group->criterias as $other_criteria) {
            if ($other_criteria->id != $criteria->id) {
                $other_criteria->seleccionado = false;
                $other_criteria->save();
            }
        }
    }

    public function render()
    {
        return view('livewire.rubric-show');
    }
}
