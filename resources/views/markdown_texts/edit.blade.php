@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit markdown text')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($markdown_text, ['route' => ['markdown_texts.update', $markdown_text->id], 'method' => 'PUT']) !!}

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
