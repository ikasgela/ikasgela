@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit link collection')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($link_collection, ['route' => ['link_collections.update', $link_collection->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
