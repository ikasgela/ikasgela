@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Nuevo curso</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => 'cursos.store']) !!}

            <div class="form-group">
                {!! Form::label('nombre', 'Nombre:') !!}
                {!! Form::text('nombre', '', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('descripcion', 'Descripción:') !!}
                {!! Form::text('descripcion', '', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::button('Guardar', ['class' => 'btn btn-primary', 'type' => 'submit']) !!}
                {!! link_to_route('cursos.index', $title = 'Cancelar', $parameters = [],
                        $attributes = ['class' => 'btn btn-link text-secondary']); !!}
            </div>

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
