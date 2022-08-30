@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit files resource')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($file_resource, ['route' => ['file_resources.update', $file_resource->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoCheck('titulo_visible', __('Show title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoCheck('descripcion_visible', __('Show description')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
