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
            <div class="card-body row pb-0" wire:key="criteria-group-{{ $criteria_group->id }}">
                <div class="col-2">
                    @if($rubric_is_editing && $criteria_group->titulo == null)
                        <h5 class="card-title border border-1 text-muted px-2">{{ __('Title') }}</h5>
                    @else
                        <h5 class="card-title">{{ $criteria_group->titulo }}</h5>
                    @endif
                    @if($rubric_is_editing && $criteria_group->descripcion == null)
                        <p class="small border border-1 text-muted px-2">{{ __('Description') }}</p>
                    @else
                        <p class="small">{{ $criteria_group->descripcion }}</p>
                    @endif
                </div>
                <div class="col">
                    <div class="row">
                        @foreach($criteria_group->criterias as $criteria)
                            <livewire:criteria-component
                                :$criteria
                                :key="'criteria-'.$criteria->id"
                                :$rubric_is_editing
                                :$rubric_is_qualifying
                            />
                        @endforeach
                        @if($rubric_is_editing)
                            <div class="col-auto align-content-center mb-3">
                                <button class="btn btn-sm btn-success h-100"
                                        wire:click="add_criteria({{ $criteria_group->id }})">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                @if($rubric_is_editing)
                    <div class="col-auto">
                        <div class="btn-toolbar">
                            <div class="btn-group-sm btn-group-vertical">
                                <button class="btn btn-primary"
                                        wire:click="up_criteria_group({{ $criteria_group->id }})">
                                    <i class="bi bi-arrow-up"></i>
                                </button>
                                <button class="btn btn-primary"
                                        wire:click="down_criteria_group({{ $criteria_group->id }})">
                                    <i class="bi bi-arrow-down"></i>
                                </button>
                            </div>
                            <div class="btn-group-sm btn-group-vertical ms-2">
                                <button class="btn btn-danger"
                                        wire:click="delete_criteria_group({{ $criteria_group->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="btn-group-sm btn-group-vertical ms-2">
                                <button class="btn btn-secondary" wire:click="toggle_edit">
                                    <i class="bi bi-power"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-auto mb-3">
                    <button
                        disabled
                        class="btn opacity-100 btn-secondary p-3" style="min-width: 4em;">
                        {{ $criteria_group->total }}
                    </button>
                </div>
            </div>
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
