<div class="row mb-3">
    <div class="col-md-12">
        {!! Form::open(['route' => ['actividades.plantillas.filtro']]) !!}
        @include('partials.desplegable_unidades')
        {!! Form::close() !!}
    </div>
</div>
