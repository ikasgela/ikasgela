<div class="row mb-3">
    <div class="col-md-12">
        {{ html()->form('POST', route('profesor.index.filtro'))->open() }}
        @include('partials.desplegable_unidades',['nombre_variable' => 'unidad_id_disponibles'])
        {{ html()->form()->close() }}
    </div>
</div>
