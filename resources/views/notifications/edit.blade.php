@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Notification settings')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['notifications.update'], 'method' => 'PUT']) !!}

            {{ Form::campoCheck('notificacion_mensaje_recibido', __('Message received'), setting_usuario('notificacion_mensaje_recibido')) }}

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

@endsection
