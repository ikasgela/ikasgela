@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New IntelliJ project')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['intellij_projects.store']]) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('host', __('Host')) }}
            {{ Form::campoTexto('repositorio', __('Repository')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
