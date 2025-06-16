<div>
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
                <div class="col-3">
                    <h5 class="card-title">{{ $criteria_group->titulo }}</h5>
                    <p class="small">{{ $criteria_group->descripcion }}</p>
                </div>
                <div class="col">
                    <div class="row btn-group">
                        @php($total = 0)
                        @foreach($criteria_group->criterias as $criteria)
                            <button
                                wire:click="seleccionar({{ $criteria->id }})"
                                class="btn {{ $criteria->seleccionado ? 'btn-primary' : 'btn-outline-primary' }} col-auto p-3">
                                {{ $criteria->texto }}
                            </button>
                            @if($criteria->seleccionado)
                                @php($total += $criteria->puntuacion)
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-1">
                    <p>{{ $total }}</p>
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
