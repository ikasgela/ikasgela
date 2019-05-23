@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New markdown text')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['markdown_texts.store']]) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('repositorio', __('Repository')) }}
            {{ Form::campoTexto('rama', __('Branch')) }}
            {{ Form::campoTexto('archivo', __('File')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
