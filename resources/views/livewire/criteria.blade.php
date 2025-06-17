<div>
    <div class="mb-3">
        @if(!Auth::user()->hasAnyRole(['admin','profesor']))
            <div class="btn-group">
                <button
                    disabled
                    class="btn opacity-100 {{ $criteria->seleccionado ? 'btn-primary' : 'btn-outline-primary' }} p-3">
                    {{ $criteria->texto }}
                </button>
                <button
                    disabled
                    class="btn opacity-100 {{ $criteria->seleccionado ? 'btn-secondary' : 'btn-outline-secondary' }} p-3">
                    {{ $criteria->puntuacion }}
                </button>
            </div>
        @elseif(!$rubric_is_editing)
            <div class="btn-group">
                <button
                    wire:click="$parent.seleccionar({{ $criteria->id }})"
                    class="btn {{ $criteria->seleccionado ? 'btn-primary' : 'btn-outline-primary' }} p-3">
                    {{ $criteria->texto }}
                </button>
                <button
                    wire:click="$parent.seleccionar({{ $criteria->id }})"
                    class="btn {{ $criteria->seleccionado ? 'btn-secondary' : 'btn-outline-secondary' }} p-3">
                    {{ $criteria->puntuacion }}
                </button>
            </div>
        @elseif(!$criteria_is_editing)
            <div class="btn-group mb-3">
                <button
                    wire:click="toggle_edit"
                    class="btn btn-primary p-3">
                    {{ $criteria->texto }}
                </button>
                <button
                    wire:click="toggle_edit"
                    class="btn btn-secondary p-3">
                    {{ $criteria->puntuacion }}
                </button>
            </div>
            <div class="btn-toolbar justify-content-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-primary" wire:click="toggle_edit">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <button class="btn btn-primary" wire:click="toggle_edit">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
                <div class="btn-group btn-group-sm ms-2">
                    <button class="btn btn-danger" wire:click="toggle_edit">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn btn-secondary">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </div>
        @else
            <form wire:submit="save">
                <div class="btn-group mb-3">
                    <textarea wire:model="texto" class="form-control" rows="5" cols="20"></textarea>
                    <input wire:model="puntuacion" type="text" class="form-control"/>
                </div>
                <div class="btn-group-sm text-center">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-floppy"></i>
                    </button>
                    <button class="btn btn-danger" wire:click="toggle_edit">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
