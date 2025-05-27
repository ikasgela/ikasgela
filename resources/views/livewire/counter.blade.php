<div>
    @include('partials.titular', ['titular' => __('Livewire Test')])

    <h1 class="display-1">{{ $count }}</h1>

    <button class="btn btn-primary" wire:click="decrement">
        <i class="bi bi-dash-lg"></i>
    </button>
    <button class="btn btn-primary" wire:click="increment">
        <i class="bi bi-plus-lg"></i>
    </button>
</div>
