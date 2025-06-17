<div>
    <div class="mb-3">
        @if($rubric_is_qualifying)
            <div class="btn-toolbar justify-content-center">
                <div class="btn-group">
                    <button
                        style="min-width: 6em; max-width: 12em; font-size: 80%;"
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
            </div>
        @elseif($rubric_is_editing)
            <div class="btn-toolbar justify-content-center">
                <div class="btn-group mb-3">
                    <button
                        style="min-width: 6em; max-width: 12em; font-size: 80%;"
                        wire:click="$dispatch('showModal', {data: {'alias' : 'edit-criteria','params' :{'criteria_id': {{ $criteria->id }} }}})"
                        class="btn btn-secondary p-3">
                        {{ $criteria->texto }}
                    </button>
                    <button
                        wire:click="$dispatch('showModal', {data: {'alias' : 'edit-criteria','params' :{'criteria_id': {{ $criteria->id }} }}})"
                        class="btn btn-secondary p-3">
                        {{ $criteria->puntuacion }}
                    </button>
                </div>
            </div>
            <div class="btn-toolbar justify-content-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <button class="btn btn-primary">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
                <div class="btn-group btn-group-sm ms-2">
                    <button class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        @else
            <div class="btn-toolbar justify-content-center">
                <div class="btn-group">
                    <button
                        style="min-width: 6em; max-width: 12em; font-size: 80%;"
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
            </div>
        @endif
    </div>
</div>
