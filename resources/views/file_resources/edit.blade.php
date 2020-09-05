@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit files resource')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($file_resource, ['route' => ['file_resources.update', $file_resource->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
