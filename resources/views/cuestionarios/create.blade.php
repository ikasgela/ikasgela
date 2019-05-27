@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New questionnaire')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['cuestionarios.store']]) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoCheck('plantilla', __('Template')) }}
            {{ Form::campoCheck('respondido', __('Answered')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
