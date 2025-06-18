<div>
    @if(Auth::user()->hasAnyRole(['admin','profesor']) && !$rubric_is_qualifying)
        <div class="mb-3">
            <button class="btn btn-secondary" wire:click="toggle_edit">
                <i class="bi bi-pencil"></i>
            </button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <div><i class="bi bi-ui-checks-grid me-2"></i>{{ __('Rubric') }}</div>
            <div>
                @include('partials.modificar_recursos', ['ruta' => 'rubrics'])
                @include('partials.editar_recurso', ['recurso' => $rubric, 'ruta' => 'rubrics'])
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $rubric->titulo }}</h5>
            <p class="card-text">{{ $rubric->descripcion }}</p>
        </div>
        <hr class="my-0">
        @foreach($rubric->criteria_groups as $criteria_group)
            <livewire:criteria-group-component
                :$criteria_group
                :key="'criteria-group-'.$criteria_group->id"
                :$rubric_is_editing
                :$rubric_is_qualifying
            />
            @if(!$loop->last)
                <hr class="my-0">
            @endif
        @endforeach
        @if($rubric_is_editing)
            <hr class="my-0">
            <div class="p-3 text-center">
                <button class="btn btn-sm btn-success w-100"
                        wire:click="add_criteria_group">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        @endif
        <hr class="my-0">
        <div class="card-body">
            <div class="row">
                <div class="col-2">
                    <h5 class="card-title">{{ __('Total') }}</h5>
                </div>
                <div class="col text-end">
                    <button
                        disabled
                        class="btn opacity-100 btn-warning p-3" style="min-width: 4em;">
                        {{ $rubric->total }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.errors')
</div>
