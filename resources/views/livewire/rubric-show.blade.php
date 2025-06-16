<div>
    <button class="btn btn-secondary" wire:click="toggle_edit">
        <i class="bi bi-pencil"></i>
    </button>

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

        @php($total_rubrica = 0)
        @foreach($rubric->criteria_groups as $criteria_group)
            <div class="card-body row">
                <div class="col-2">
                    <h5 class="card-title">{{ $criteria_group->titulo }}</h5>
                    <p class="small">{{ $criteria_group->descripcion }}</p>
                </div>
                <div class="col">
                    <div class="row">
                        @php($total = 0)
                        @foreach($criteria_group->criterias as $criteria)
                            <div class="col-auto">
                                <livewire:criteria-component
                                    :$criteria
                                    :key="$criteria->id"
                                    :$rubric_is_editing
                                    :$total/>
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
                <div class="col-auto">
                    <p>{{ $criteria_group->total }}</p>
                </div>
            </div>
            @php($total_rubrica += $total)
            @if(!$loop->last)
                <hr class="my-0">
            @endif
        @endforeach
        <div class="card-footer">
            <div class="row">
                <div class="col">
                </div>
                <div class="col-1">
                    <p>{{ $total_rubrica }}</p>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.errors')
</div>
