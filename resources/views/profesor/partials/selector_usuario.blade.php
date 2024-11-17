<div class="d-flex justify-content-between mb-3">
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

    <div class="flex-fill">
        @include('profesor.partials.tarjeta_usuario')
    </div>

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
