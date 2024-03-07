<div class="card">
    <div class="card-body">
        @if(str_starts_with(\Route::current()->getName(),'results.pdf'))
            <h3 class="card-title">{{ __('Applied qualification criteria') }}</h3>
        @else
            <h5 class="card-title">{{ __('Applied qualification criteria') }}</h5>
        @endif
        <ul class="card-text mb-1">
            @if($calificaciones->hay_nota_manual)
                <li>{{ __('Qualification has been manually established.') }}</li>
            @elseif($calificaciones->examen_final)
                <li>{!! __('At least :porcentaje_minimo&thinsp;% must be achieved on the final evaluation tests, been able to recover a maximum :porcentaje_recuperable&thinsp;% of the calification.', [
                        'porcentaje_minimo'=> formato_decimales($curso->minimo_examenes_finales),
                        'porcentaje_recuperable'=> formato_decimales($curso->maximo_recuperable_examenes_finales),
                    ]) !!}
                    @include('results.partials.criterio_superado', ['criterio' => $calificaciones->examen_final_superado])
                </li>
            @else
                @if($curso->minimo_entregadas > 0)
                    <li>{!! __('At least :porcentaje_minimo&thinsp;% of the activities proposed on each unit must have been made.', [
                            'porcentaje_minimo'=> formato_decimales($curso->minimo_entregadas),
                        ]) !!}
                        @include('results.partials.criterio_superado', ['criterio' => $calificaciones->actividades_obligatorias_superadas])
                    </li>
                @endif
                @if($curso->minimo_competencias > 0)
                    <li>{!! __('At least :porcentaje_minimo&thinsp;% must be achieved on each skill individually.', [
                            'porcentaje_minimo'=> formato_decimales($curso->minimo_competencias),
                        ]) !!}
                        @include('results.partials.criterio_superado', ['criterio' => $calificaciones->competencias_50_porciento])
                    </li>
                @endif
                @if($curso->examenes_obligatorios)
                    <li>{!! __('At least :porcentaje_minimo&thinsp;% must be achieved on the tests that require it.', [
                            'porcentaje_minimo'=> formato_decimales($curso->minimo_examenes),
                        ]) !!}
                        @include('results.partials.criterio_superado', ['criterio' => $calificaciones->pruebas_evaluacion])
                    </li>
                @else
                    <li>{{ __('There are no evaluation tests that require to achieve a minimal percent.') }}</li>
                @endif
                @php($ajuste_proporcional_nota = $milestone?->ajuste_proporcional_nota ?: $curso?->ajuste_proporcional_nota)
                @switch($ajuste_proporcional_nota)
                    @case('media')
                        <li>{{ __('The qualification is adjusted proportionally to the average of mandatory activities completed by the group.') }}</li>
                        @break
                    @case('mediana')
                        <li>{{ __('The qualification is adjusted proportionally to the median of mandatory activities completed by the group.') }}</li>
                        @break
                    @default
                        <li>{{ __('The qualification is adjusted proportionally to the number of mandatory activities.') }}</li>
                @endswitch
            @endif
        </ul>
    </div>
</div>
