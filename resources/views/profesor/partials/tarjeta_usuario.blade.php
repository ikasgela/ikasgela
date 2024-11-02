<div class="flex-fill">
    {{-- Tarjeta --}}
    <div class="card m-0">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-10 p-3">
                    <h5 class="card-title">
                        <a title="{{ __('Control panel') }}"
                           href="{{ route('profesor.tareas', ['user' => $user->id]) }}"
                           class='text-dark'>{{ $user->full_name }}</a>
                        @include('profesor.partials.status_usuario')
                        @include('profesor.partials.etiquetas_usuario')
                        @include('profesor.partials.baja_ansiedad_usuario')
                        @include('profesor.partials.acciones_usuario')
                    </h5>
                    @include('partials.mailto', ['user' => $user, 'format' => 'class="card-link"'])
                </div>
                @if(config('ikasgela.avatar_enabled'))
                    <div class="col-12 col-sm-2 text-center text-sm-right">
                        @include('users.partials.avatar', ['user' => $user, 'width' => 100])
                    </div>
                @endif
            </div>
        </div>
    </div>
    {{-- Fin tarjeta--}}
</div>
