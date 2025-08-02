@use(Illuminate\Support\Str)
<td class="clickable">
    @if(!is_null($actividad))
        {!! !is_null($actividad->siguiente) ? $actividad->final
        ? '<i class="bi bi-x text-danger"></i>'
        : '<i class="fas fa-arrow-right text-success"></i>'
        : '' !!}
        &nbsp;
        @if(!is_null($actividad->siguiente))
            <span title="{{ $actividad->siguiente->slug . ' ('.$actividad->siguiente->id.')' }}">
                {{ Str::limit($actividad->siguiente->slug, 20) }}
            </span>
        @endif
    @endif
</td>
