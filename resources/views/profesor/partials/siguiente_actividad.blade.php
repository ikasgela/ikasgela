<td class="clickable">
    @if(!is_null($actividad))
        {!! !is_null($actividad->siguiente) ? $actividad->final
        ? '<i class="fas fa-times text-danger"></i>'
        : '<i class="fas fa-arrow-right text-success"></i>'
        : '' !!}
        &nbsp;
        @if(!is_null($actividad->siguiente))
            {{ $actividad->siguiente->slug . ' ('.$actividad->siguiente->id.')' }}
        @endif
    @endif
</td>
