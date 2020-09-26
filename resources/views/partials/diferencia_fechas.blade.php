@if(!is_null($fecha_inicial) && !is_null($fecha_final))
    @php($diff_days = $fecha_inicial->diffInDays($fecha_final, false))
    @php($diff_seconds = $fecha_inicial->diffInSeconds($fecha_final, false))
    @if($diff_seconds > 0)
        {{ trans_choice('fechas.remaining', $diff_days, ['days' => $diff_days]) }}
    @else
        {{ __('fechas.expired') }}
    @endif
@endif
