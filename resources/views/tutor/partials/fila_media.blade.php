<tr class="bg-secondary">
    <td colspan="4"></td>
    @php($ajuste_proporcional_nota = $milestone?->ajuste_proporcional_nota ?: $curso?->ajuste_proporcional_nota)
    @switch($ajuste_proporcional_nota)
        @case('mediana')
            <td class="text-center text-dark">{{ __('Median') }}: {{ $mediana_formato }}</td>
            @break
        @default
            <td class="text-center text-dark">{{ __('Mean') }}: {{ $media_actividades_grupo_formato }}</td>
    @endswitch
    <td colspan="{{ $unidades->count() + 4 }}"></td>
</tr>
