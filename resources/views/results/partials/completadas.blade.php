{{ __('Completed activities') }}: {{ $calificaciones->numero_actividades_completadas }}
@if(!Auth::user()->baja_ansiedad)
    @php($ajuste_proporcional_nota = $milestone?->ajuste_proporcional_nota ?: $curso?->ajuste_proporcional_nota)
    @switch($ajuste_proporcional_nota)
        @case('mediana')
            - {{ __('Group median') }}: {{ $mediana_actividades_grupo }}
            @break
        @default
            - {{ __('Group mean') }}: {{ $media_actividades_grupo }}
    @endswitch
@endif
