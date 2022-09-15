@if(Auth::user()->hasAnyRole(['admin','profesor']))
    <div class='btn-group-sm'>
        @if(isset($actividad_siguiente))
            <a class="btn btn-primary btn-sm"
               href="{{ route('actividades.preview', $actividad_anterior) }}">
                <i class="fas fa-arrow-left"></i>
            </a>
        @else
            <a class="btn btn-light disabled" href="#">
                <i class="fas fa-arrow-left"></i>
            </a>
        @endif
        @if(isset($actividad_siguiente))
            <a class="btn btn-primary btn-sm"
               href="{{ route('actividades.preview', $actividad_siguiente) }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        @else
            <a class="btn btn-light disabled" href="#">
                <i class="fas fa-arrow-right"></i>
            </a>
        @endif
    </div>
@endif
