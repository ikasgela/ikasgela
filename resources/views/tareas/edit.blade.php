@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit task')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($tarea, ['route' => ['tareas.update', $tarea->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('estado', __('Status')) }}
            {{ Form::campoTextoLabel('aceptada', __('Accepted')) }}
            {{ Form::campoTexto('fecha_limite', __('Deadline')) }}
            {{ Form::campoTextoLabel('enviada', __('Sent')) }}
            {{ Form::campoTextoLabel('revisada', __('Reviewed')) }}
            {{ Form::campoTextArea('feedback', __('Feedback')) }}
            {{ Form::campoTexto('puntuacion', __('Score')) }}
            {{ Form::campoTextoLabel('terminada', __('Finished')) }}
            {{ Form::campoTextoLabel('archivada', 'Archivada') }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
