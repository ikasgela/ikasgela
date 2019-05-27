@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit questionnaire')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($cuestionario, ['route' => ['cuestionarios.update', $cuestionario->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoCheck('plantilla', __('Template')) }}
            {{ Form::campoCheck('respondido', __('Answered')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Questions')])

    @include('preguntas.tabla')

    <a class="btn btn-primary"
       href="{{ route('preguntas.anyadir', ['cuestionario_id'=>$cuestionario->id]) }}">{{ __('Add question') }}</a>

@endsection
