<?php

namespace App\Livewire;

use App\Models\Criteria;
use App\Models\CriteriaGroup;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CriteriaGroupComponent extends Component
{
    public CriteriaGroup $criteria_group;

    #[Reactive]
    public $rubric_is_editing = false;

    #[Reactive]
    public $rubric_is_qualifying = false;

    public $is_editing = false;

    public $titulo;
    public $descripcion;

    public function mount()
    {
        $this->titulo = $this->criteria_group->titulo;
        $this->descripcion = $this->criteria_group->descripcion;
    }

    public function render()
    {
        return view('livewire.criteria-group-component');
    }

    #[Computed]
    public function is_first_criteria_group($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);
        $orden = $criteria_group->rubric->criteria_groups()->min('orden');
        return $criteria_group->orden == $orden;
    }

    #[Computed]
    public function is_last_criteria_group($criteria_group_id)
    {
        $criteria_group = CriteriaGroup::findOrFail($criteria_group_id);
        $orden = $criteria_group->rubric->criteria_groups()->max('orden');
        return $criteria_group->orden == $orden;
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

    public function toggle_edit()
    {
        $this->is_editing = !$this->is_editing;
    }

    public function save()
    {
        $this->is_editing = false;
        $this->criteria_group->titulo = $this->titulo;
        $this->criteria_group->descripcion = $this->descripcion;
        $this->criteria_group->save();
    }
}
