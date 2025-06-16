<?php

namespace App\Livewire;

use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Support\Str;
use Livewire\Component;

class RubricComponent extends Component
{
    public Rubric $rubric;
    public $rubric_is_editing = false;

    public function mount(Rubric $rubric)
    {
        $this->rubric = $rubric;
    }

    public function seleccionar($criteria_id)
    {
        $criteria = Criteria::findOrFail($criteria_id);
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


    public function add_criteria($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);

        Criteria::create([
            'texto' => __('Write your criteria here'),
            'puntuacion' => 0,
            'orden' => Str::ulid(),
            'criteria_group_id' => $criteria_group->id,
        ]);
    }

    public function toggle_edit()
    {
        $this->rubric_is_editing = !$this->rubric_is_editing;
    }


    public function render()
    {
        return view('livewire.rubric-show');
    }
}
