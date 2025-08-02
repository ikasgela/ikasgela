@if(Auth::user()->hasAnyRole(['admin','profesor']))
    <div class="btn-group">
        @if( !is_null($actividad->siguiente) )
            <a class="btn-sm btn-link" href="{{ route('actividades.preview', $actividad->siguiente->id) }}">
                {!! $actividad->final
                        ? '<i class="bi bi-x text-danger mx-2"></i>'
                        : '<i class="bi bi-arrow-right text-success mx-2"></i>'
                !!}{{ $actividad->siguiente->slug . ' ('.$actividad->siguiente->id.')' }}</a>
        @endif
    </div>
@endif
