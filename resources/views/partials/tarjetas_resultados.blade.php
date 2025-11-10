<hr class="m-0">
<div class="row px-3 pt-3">
    @foreach($actividad->recursos as $recurso)
        <div class="col-md-{{ $recurso->pivote($actividad)->columnas ?: 6 }}">
            @switch($recurso::class)
                @case('App\Models\Rubric')
                    @livewire('rubric-component', [
                        'actividad' => $actividad,
                        'rubric' => $recurso,
                    ])
                    @break
                @case('App\Models\TestResult')
                    @include('test_results.tarjeta', ['test_result' => $recurso])
                    @break
            @endswitch
        </div>
    @endforeach
</div>
