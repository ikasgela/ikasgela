@if(!is_null($fecha_inicial) && !is_null($fecha_final))
    @php($diff_days = formato_decimales($fecha_inicial->diffInDays($fecha_final, false)))
    @php($diff_seconds = $fecha_inicial->diffInSeconds($fecha_final, false))
    @if($diff_seconds > 0)
        {{ trans_choice('fechas.remaining', $diff_days, ['days' => $diff_days]) }}
    @else
        {{ trans('fechas.expired') }}
    @endif
@endif
