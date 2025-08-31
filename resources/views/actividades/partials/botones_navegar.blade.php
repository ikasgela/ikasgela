@if(Auth::user()->hasAnyRole(['admin','profesor']))
    <div class='btn-group-sm'>
        @if(isset($actividades_ids[$actual-1]))
            <a class="btn btn-primary btn-sm"
               title="{{ __('Previous') }}"
               href="{{ route('actividades.preview', $actividades_ids[$actual-1]) }}">
                <i class="bi bi-arrow-left"></i>
            </a>
        @else
            <a class="btn btn-light btn-sm disabled" href="#">
                <i class="bi bi-arrow-left"></i>
            </a>
        @endif
        @if(isset($actividades_ids[$actual+1]))
            <a class="btn btn-primary btn-sm"
               title="{{ __('Next') }}"
               href="{{ route('actividades.preview', $actividades_ids[$actual+1]) }}">
                <i class="bi bi-arrow-right"></i>
            </a>
        @else
            <a class="btn btn-light btn-sm disabled" href="#">
                <i class="bi bi-arrow-right"></i>
            </a>
        @endif
    </div>
@endif
