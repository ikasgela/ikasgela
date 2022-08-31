@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New files resource')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['file_resources.store']]) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
