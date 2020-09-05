@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New image upload')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['file_uploads.store']]) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('max_files', __('Maximum'), 1) }}
            {{ Form::campoCheck('plantilla', __('Template'), true) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
