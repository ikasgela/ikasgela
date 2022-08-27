<div class="card-header text-white bg-dark d-flex justify-content-between">
    <span>{{ $actividad->unidad->curso->nombre }} » {{ $actividad->unidad->nombre }}</span>
    @if(isset($actividad->fecha_entrega) && !$actividad->tarea->is_completada && !$actividad->tarea->is_enviada)
        @if(!$actividad->is_finished)
            <div>{{ __('Remaining time') }}:
                <span data-countdown="{{ $actividad->fecha_entrega }}"></span>
            </div>
        @else
            <span>
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            {{ __('Task expired') }}
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </span>
        @endif
    @endif
    @if(isset($num_actividad))
        <span>{{ $num_actividad }} {{ __('of') }} {{count($actividades)}}</span>
    @endif
</div>
