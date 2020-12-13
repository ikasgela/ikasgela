<div class="row mb-3">
    <div class="col-md-12">
        {!! Form::open(['route' => ['profesor.tareas.filtro', $user->id]]) !!}
        @include('partials.desplegable_unidades',['nombre_variable' => $nombre_variable])
        {!! Form::close() !!}
    </div>
</div>
