<div class="mb-3">
    <div>
        @if(!$rubric_edit_mode)
            <div class="btn-group">
                <button
                    wire:click="seleccionar({{ $criteria->id }})"
                    class="btn {{ $criteria->seleccionado ? 'btn-primary' : 'btn-outline-primary' }} p-3">
                    {{ $criteria->texto }}
                </button>
                <button
                    class="btn {{ $criteria->seleccionado ? 'btn-secondary' : 'btn-outline-secondary' }} p-3">
                    {{ $criteria->puntuacion }}
                </button>
            </div>
        @elseif($rubric_edit_mode && !$is_editing)
            <div class="btn-group mb-3">
                <button
                    wire:click="toggle_edit"
                    class="btn {{ $criteria->seleccionado ? 'btn-primary' : 'btn-outline-primary' }} p-3">
                    {{ $criteria->texto }}
                </button>
                <button
                    wire:click="toggle_edit"
                    class="btn {{ $criteria->seleccionado ? 'btn-secondary' : 'btn-outline-secondary' }} p-3">
                    {{ $criteria->puntuacion }}
                </button>
            </div>
            <div class="btn-group-sm text-center">
                <button class="btn btn-secondary" wire:click="toggle_edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger" wire:click="toggle_edit">
                    <i class="bi bi-trash"></i>
                </button>
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
