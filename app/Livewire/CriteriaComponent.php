<?php

namespace App\Livewire;

use App\Models\Criteria;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CriteriaComponent extends Component
{
    #[Reactive]
    public Criteria $criteria;

    #[Reactive]
    public $rubric_is_editing = false;

    public function render()
    {
        return view('livewire.criteria');
    }

    #[On('hideModal')]
    public function refresh()
    {
    }
}
