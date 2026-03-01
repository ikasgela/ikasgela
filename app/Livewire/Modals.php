<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Modals extends Component
{
    public ?string $alias = null;
    public string $size = "modal-lg";
    public array $params = [];
    public ?string $activeModal = null;

    #[On('showModal')]
    public function showModal($data)
    {
        if (!is_array($data) || empty($data['alias']) || !is_string($data['alias'])) {
            return;
        }

        $this->alias = $data['alias'];

        if (isset($data['size']) && is_string($data['size'])) {
            $this->size = $data['size'];
        }

        $this->params = is_array($data['params'] ?? null) ? $data['params'] : [];
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
