<tr class="bg-secondary">
    <td colspan="4"></td>
    <td class="text-center text-dark">{{ __('Mean') }}: {{ $media_actividades_grupo_formato }}</td>
    <td></td>
    @if(Auth::user()->hasAnyRole(['profesor','admin']))
        <td></td>
    @endif
    <td colspan="{{ $unidades->count() }}"></td>
</tr>
