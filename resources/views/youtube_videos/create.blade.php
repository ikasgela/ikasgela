@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New YouTube video')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['youtube_videos.store']]) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('codigo', __('Code')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
