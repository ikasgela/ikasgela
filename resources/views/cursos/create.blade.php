@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New course')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['cursos.store']]) !!}

            {{ Form::campoTexto('nombre', __('Name')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
