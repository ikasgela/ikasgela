@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Notification settings'), 'subtitulo' => ''])

    {!! Form::open(['route' => ['notifications.update'], 'method' => 'PUT']) !!}

    <p>{{ __('Enable or disable email notifications.') }}</p>

    @include('partials.subtitulo', ['subtitulo' => __('Tutorship')])

    <div class="card">
        <div class="card-body pb-1">

            {{ Form::campoCheck('notificacion_mensaje_recibido', __('Message received'),
            setting_usuario('notificacion_mensaje_recibido')) }}

        </div>
    </div>

    @if(Auth::user()->hasRole('alumno'))

        @include('partials.subtitulo', ['subtitulo' => __('Activities')])

        <div class="card">
            <div class="card-body pb-1">

                {{ Form::campoCheck('notificacion_actividad_asignada', __('Activity assigned'),
                setting_usuario('notificacion_actividad_asignada')) }}

                {{ Form::campoCheck('notificacion_feedback_recibido', __('Feedback received'),
                setting_usuario('notificacion_feedback_recibido')) }}

            </div>
        </div>

    @endif

    @if(Auth::user()->hasRole('profesor'))

        @include('partials.subtitulo', ['subtitulo' => __('Teacher')])

        <div class="card">
            <div class="card-body pb-1">

                {{ Form::campoCheck('notificacion_tarea_enviada', __('Task for review'),
                setting_usuario('notificacion_tarea_enviada')) }}

            </div>
        </div>

    @endif

    <div class="form-group">
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </div>

    {!! Form::close() !!}

    @if(Auth::user()->hasRole('admin'))

        @include('partials.subtitulo', ['subtitulo' => __('Notification test')])

        <a href="{{ route('notifications.test') }}" class="btn btn-success single_click">
            <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Send') }}
        </a>

    @endif

@endsection
