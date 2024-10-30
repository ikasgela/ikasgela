<div class="row mb-3">
    <div class="col-md-12">
        {{ html()->form('POST', route('profesor.tareas.filtro', $user->id))->open() }}
        @include('partials.desplegable_unidades',['nombre_variable' => $nombre_variable])
        {{ html()->form()->close() }}
    </div>
</div>
