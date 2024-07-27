<div class="d-flex flex-row mb-4">
    <div class="align-self-center me-3">
        @if(isset($user_anterior))
            <a class="btn btn-primary" href="{{ route('profesor.tareas', [$user_anterior]) }}">
                <i class="fas fa-arrow-left"></i>
            </a>
        @else
            <a class="btn btn-light disabled" href="#">
                <i class="fas fa-arrow-left"></i>
            </a>
        @endif
    </div>

    @include('profesor.partials.tarjeta_usuario')

    <div class="align-self-center ms-3">
        @if(isset($user_siguiente))
            <a class="btn btn-primary" href="{{ route('profesor.tareas', [$user_siguiente]) }}">
                <i class="fas fa-arrow-right"></i>
            </a>
        @else
            <a class="btn btn-light disabled" href="#">
                <i class="fas fa-arrow-right"></i>
            </a>
        @endif
    </div>
</div>
