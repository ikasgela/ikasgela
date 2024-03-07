<p>
    {{ __('Proportional calification adjustment') }}:
    @php($ajuste_proporcional_nota = $milestone?->ajuste_proporcional_nota ?: $curso?->ajuste_proporcional_nota)
    @switch($ajuste_proporcional_nota)
        @case('media')
            {{ __('Average') }}
            @break
        @case('mediana')
            {{ __('Median') }}
            @break
        @default
            {{ __('Undefined') }}
    @endswitch
</p>
<p>
    {{ __('Normalize calification') }}:
    {{ $curso?->normalizar_nota || $milestone?->normalizar_nota ? __('Yes'): __('No') }}
</p>
