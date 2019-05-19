@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit task')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($tarea, ['route' => ['tareas.update', $tarea->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('estado', __('Status')) }}
            {{ Form::campoTexto('fecha_limite', __('Deadline')) }}
            {{ Form::campoTextArea('feedback', __('Feedback')) }}
            {{ Form::campoTexto('puntuacion', __('Score')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
