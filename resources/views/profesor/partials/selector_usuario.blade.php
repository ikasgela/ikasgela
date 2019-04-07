<div class="d-flex flex-row mb-4">
    <div class="align-self-center">
        @if(isset($user_anterior))
            <a class="btn btn-primary" href="{{ route('profesor.tareas', [$user_anterior]) }}"><i
                        class="fas fa-arrow-left"></i></a>
        @else
            <a class="btn btn-light disabled" href="#"><i
                        class="fas fa-arrow-left"></i></a>
        @endif
    </div>
    <div class="flex-fill mx-3">
        {{-- Tarjeta --}}
        <div class="card m-0">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="flex-fill">
                        <h5 class="card-title">{{ $user->name }}</h5>
                        <a href="mailto:{{ $user->email }}" class="card-link">{{ $user->email }}</a>
                    </div>
                    <div>
                        <img style="width:100px;" src="{{ $user->avatar_url()}}">
                    </div>
                </div>
            </div>
        </div>
        {{-- Fin tarjeta--}}
    </div>
    <div class="align-self-center">
        @if(isset($user_siguiente))
            <a class="btn btn-primary" href="{{ route('profesor.tareas', [$user_siguiente]) }}"><i
                        class="fas fa-arrow-right"></i></a>
        @else
            <a class="btn btn-light disabled" href="#"><i
                        class="fas fa-arrow-right"></i></a>
        @endif
    </div>
</div>
