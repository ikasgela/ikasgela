<div>
    <div class="mb-3">
        <button class="btn btn-secondary" wire:click="toggle_edit">
            <i class="bi bi-pencil"></i>
        </button>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <div><i class="bi bi-ui-checks-grid me-2"></i>{{ $rubric->titulo }}</div>
            {{--
                        <div>
                            @include('partials.modificar_recursos', ['ruta' => 'rubrics'])
                            @include('partials.editar_recurso', ['recurso' => $rubric, 'ruta' => 'rubrics'])
                        </div>
            --}}
        </div>

        @foreach($rubric->criteria_groups as $criteria_group)
            <div class="card-body row pb-0">
                <div class="col-2">
                    <h5 class="card-title">{{ $criteria_group->titulo }}</h5>
                    <p class="small">{{ $criteria_group->descripcion }}</p>
                </div>
                <div class="col">
                    <div class="row">
                        @foreach($criteria_group->criterias as $criteria)
                            <div class="col-auto">
                                <livewire:criteria-component
                                    :$criteria
                                    :key="$criteria->id"
                                    :$rubric_is_editing/>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if($rubric_is_editing)
                    <div class="col-auto">
                        <button class="btn btn-secondary"
                                wire:click="add_criteria({{ $criteria_group->id }})">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                @endif
                <div class="col-1 d-flex justify-content-center align-items-center mb-3">
                    <span class="badge text-bg-secondary fs-6">{{ $criteria_group->total }}</span>
                </div>
            </div>
            @if(!$loop->last)
                <hr class="my-0">
            @endif
        @endforeach
        <div class="card-footer">
            <div class="row">
                <div class="col">
                </div>
                <div class="col-1 d-flex justify-content-center align-items-center py-3">
                    <span class="badge text-bg-warning fs-6">{{ $rubric->total }}</span>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.errors')
</div>
