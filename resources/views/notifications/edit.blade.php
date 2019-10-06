@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Notification settings')])

    <div class="card">
        <div class="card-body pb-1">

            {!! Form::open(['route' => ['notifications.update'], 'method' => 'PUT']) !!}

            {{ Form::campoCheck('enviar_emails', __('Global'), $user->enviar_emails) }}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Tutorship')])

    <div class="card">
        <div class="card-body pb-1">

            {{ Form::campoCheck('notificacion_mensaje_recibido', __('Message received'),
            setting_usuario('notificacion_mensaje_recibido'),
            [ !$user->enviar_emails ? 'disabled' : '' ]) }}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Activities')])

    <div class="card">
        <div class="card-body pb-1">

            {{ Form::campoCheck('notificacion_mensaje_recibido', __('Message received'),
            setting_usuario('notificacion_mensaje_recibido'),
            [ !$user->enviar_emails ? 'disabled' : '' ]) }}

        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </div>

    {!! Form::close() !!}

@endsection
