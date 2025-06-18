<?php

namespace App\Livewire;

use App\Models\Criteria;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CriteriaComponent extends Component
{
    #[Reactive]
    public Criteria $criteria;

    #[Reactive]
    public $rubric_is_editing = false;

    #[Reactive]
    public $rubric_is_qualifying = false;

    public function render()
    {
        return view('livewire.criteria');
    }

    #[On('hideModal')]
    public function refresh()
    {
    }

    #[Computed]
    public function is_first_in_group()
    {
        $criteria = Criteria::find($this->criteria->id);
        $orden = $criteria->criteria_group->criterias()->min('orden');
        return $criteria->orden == $orden;
    }

    #[Computed]
    public function is_last_in_group()
    {
        $criteria = Criteria::find($this->criteria->id);
        $orden = $criteria->criteria_group->criterias()->max('orden');
        return $criteria->orden == $orden;
    }
}
