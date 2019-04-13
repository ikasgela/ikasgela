@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit user')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('email', __('Email')) }}
            {{ Form::campoTexto('username', __('Username')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
