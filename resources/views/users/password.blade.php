@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Change password')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($user, ['route' => ['users.update.password', $user->id], 'method' => 'PUT']) !!}

            {{ Form::campoTextoLabel('username', __('Username')) }}

            {{ Form::campoPassword('password', __('New password')) }}
            {{ Form::campoPassword('password_confirmation', __('Password confirmation')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
