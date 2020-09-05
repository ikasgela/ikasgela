@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit image upload')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($file_upload, ['route' => ['file_uploads.update', $file_upload->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('max_files', __('Maximum')) }}
            {{ Form::campoCheck('plantilla', __('Template')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
