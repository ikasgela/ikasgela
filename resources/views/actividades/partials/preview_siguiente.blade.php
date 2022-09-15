@if(Auth::user()->hasAnyRole(['admin','profesor']))
    <div>
        {!! !is_null($actividad->siguiente) ? $actividad->final
                ? '<i class="fas fa-times text-danger mx-2"></i>'
                : '<i class="fas fa-arrow-right text-success mx-2"></i>'
                : '' !!}
        @if( !is_null($actividad->siguiente) )
            <a href="{{ route('actividades.preview', $actividad->siguiente->id) }}">{{ $actividad->siguiente->slug . ' ('.$actividad->siguiente->id.')' }}</a>
        @endif
    </div>
@endif
