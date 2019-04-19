@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit course')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($curso, ['route' => ['cursos.update', $curso->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('nombre', __('Name')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
