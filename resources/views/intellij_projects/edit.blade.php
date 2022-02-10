@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit IntelliJ project')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($intellij_project, ['route' => ['intellij_projects.update', $intellij_project->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('host', __('Host')) }}
            {{ Form::campoTexto('open_with', __('Open with')) }}
            {{ Form::campoTexto('repositorio', __('Repository')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
