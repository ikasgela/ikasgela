<?php

namespace App\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Modals extends Component
{
    #[Locked]
    public ?string $alias = null;

    #[Locked]
    public string $size = "modal-lg";

    #[Locked]
    public array $params = [];

    #[Locked]
    public ?string $activeModal = null;

    #[On('showModal')]
    public function showModal($alias = null, $params = [], $size = null)
    {
        if (empty($alias) || !is_string($alias)) {
            return;
        }

        $this->alias = $alias;

        if ($size !== null && is_string($size)) {
            $this->size = $size;
        }

        $this->params = is_array($params) ? $params : [];
        $this->activeModal = "modal-id-" . rand();
        $this->dispatch('showBootstrapModal');
    }

    #[On('resetModal')]
    public function resetModal()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.modals');
    }
}
