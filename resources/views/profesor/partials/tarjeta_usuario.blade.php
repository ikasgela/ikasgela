<div class="flex-fill">
    {{-- Tarjeta --}}
    <div class="card m-0">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 p-3">
                    <h5 class="card-title">
                        {{ $user->name }} {{ $user->surname }}
                        @include('profesor.partials.status_usuario')
                        @include('profesor.partials.acciones_usuario')
                    </h5>
                    <a href="mailto:{{ $user->email }}" class="card-link">{{ $user->email }}</a>
                </div>
                <div class="col-12 col-sm-6 text-center text-sm-right">
                    @include('users.partials.avatar', ['user' => $user, 'width' => 100])
                </div>
            </div>
        </div>
    </div>
    {{-- Fin tarjeta--}}
</div>
