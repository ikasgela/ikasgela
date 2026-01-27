<div class="me-3">
    @if(isset($actividad_anterior))
        <a class="btn btn-primary"
           title="{{ __('Previous') }}"
           href="{{ route('profesor.revisar', ['user' => $user->id, 'tarea' => $actividad_anterior]) }}">
            <i class="bi bi-arrow-left"></i>
        </a>
    @else
        <a class="btn btn-light disabled" href="#">
            <i class="bi bi-arrow-left"></i>
        </a>
    @endif
    @if(isset($actividad_siguiente))
        <a class="btn btn-primary"
           title="{{ __('Next') }}"
           href="{{ route('profesor.revisar', ['user' => $user->id, 'tarea' => $actividad_siguiente]) }}">
            <i class="bi bi-arrow-right"></i>
        </a>
    @else
        <a class="btn btn-light disabled" href="#">
            <i class="bi bi-arrow-right"></i>
        </a>
    @endif
</div>
