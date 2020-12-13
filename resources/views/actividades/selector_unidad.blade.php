<div class="row mb-3">
    <div class="col-md-12">
        {!! Form::open(['route' => ['actividades.plantillas.filtro']]) !!}
        @include('partials.desplegable_unidades',['nombre_variable' => 'unidad_id_disponibles'])
        {!! Form::close() !!}
    </div>
</div>
