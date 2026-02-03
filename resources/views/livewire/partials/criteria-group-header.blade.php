@if($rubric_is_editing && $is_editing)
    <input type="text" class="form-control mb-2" wire:model="titulo" wire:keydown.enter="save"
           placeholder="{{ __('Title') }}"/>
@elseif($rubric_is_editing && !$titulo)
    <a wire:click.prevent="toggle_edit">
        <h5 class="card-title border border-1 text-muted px-2">{{ __('Title') }}</h5>
    </a>
@elseif($titulo)
    <a wire:click.prevent="toggle_edit">
        <h5 class="card-title">{{ $criteria_group->titulo }}</h5>
    </a>
@endif
@if($rubric_is_editing && $is_editing)
    <input type="text" class="form-control" wire:model="descripcion" wire:keydown.enter="save"
           placeholder="{{ __('Description') }}"/>
@elseif($rubric_is_editing && !$descripcion)
    <a wire:click.prevent="toggle_edit">
        <p class="small border border-1 text-muted px-2">{{ __('Description') }}</p>
    </a>
@elseif($descripcion)
    <a wire:click.prevent="toggle_edit">
        <span class="small">{{ $criteria_group->descripcion }}</span>
    </a>
@endif
@if($cabecera_horizontal && ($titulo || $descripcion))
    <div class="mb-3"></div>
@endif
