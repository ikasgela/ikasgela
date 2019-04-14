@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit YouTube video')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($youtube_video, ['route' => ['youtube_videos.update', $youtube_video->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('codigo', __('Code')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
