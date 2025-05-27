<div>
    @include('partials.titular', ['titular' => __('Livewire Test')])

    @include('profesor.partials.tarjeta_usuario')

    <button class="btn btn-primary" wire:click="decrement">
        <i class="bi bi-dash-lg"></i>
    </button>
    <span class="mx-3">{{ $position + 1 }} de {{ $users->count() }}</span>
    <button class="btn btn-primary" wire:click="increment">
        <i class="bi bi-plus-lg"></i>
    </button>
</div>
