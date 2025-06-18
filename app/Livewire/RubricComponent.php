<?php

namespace App\Livewire;

use App\Models\Actividad;
use App\Models\Criteria;
use App\Models\CriteriaGroup;
use App\Models\Rubric;
use Illuminate\Support\Str;
use Livewire\Component;

class RubricComponent extends Component
{
    public ?Actividad $actividad;
    public Rubric $rubric;
    public $rubric_is_editing = false;
    public $rubric_is_qualifying = false;

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
            'orden' => Str::orderedUuid(),
            'criteria_group_id' => $criteria_group->id,
        ]);
    }

    public function delete_criteria($criteria_id)
    {
        $criteria = Criteria::findOrFail($criteria_id);
        $criteria->delete();
    }

    public function left_criteria($criteria_id)
    {
        $c1 = Criteria::findOrFail($criteria_id);
        $orden = $c1->criteria_group->criterias()->where('orden', '<', $c1->orden)->max('orden');
        $c2 = Criteria::where('orden', $orden)->first();

        if ($c2 != null) {
            $temp = $c1->orden;
            $c1->orden = $c2->orden;
            $c2->orden = $temp;

            $c1->save();
            $c2->save();
        }
    }

    public function right_criteria($criteria_id)
    {
        $c1 = Criteria::findOrFail($criteria_id);
        $c2 = $c1->criteria_group->criterias()->where('orden', '>', $c1->orden)->first();

        if ($c2 != null) {
            $temp = $c1->orden;
            $c1->orden = $c2->orden;
            $c2->orden = $temp;

            $c1->save();
            $c2->save();
        }
    }

    public function add_criteria_group()
    {
        CriteriaGroup::create([
            'titulo' => '',
            'descripcion' => '',
            'orden' => Str::orderedUuid(),
            'rubric_id' => $this->rubric->id,
        ]);
    }

    public function delete_criteria_group($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);
        $criteria_group->delete();
    }

    public function up_criteria_group($criteria_group_id)
    {
        $c1 = CriteriaGroup::findOrFail($criteria_group_id);
        $orden = $this->rubric->criteria_groups()->where('orden', '<', $c1->orden)->max('orden');
        $c2 = CriteriaGroup::where('orden', $orden)->first();

        if ($c2 != null) {
            $temp = $c1->orden;
            $c1->orden = $c2->orden;
            $c2->orden = $temp;

            $c1->save();
            $c2->save();
        }
    }

    public function down_criteria_group($criteria_group_id)
    {
        $c1 = CriteriaGroup::findOrFail($criteria_group_id);
        $c2 = $this->rubric->criteria_groups()->where('orden', '>', $c1->orden)->first();

        if ($c2 != null) {
            $temp = $c1->orden;
            $c1->orden = $c2->orden;
            $c2->orden = $temp;

            $c1->save();
            $c2->save();
        }
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
