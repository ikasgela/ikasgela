@extends('layouts.app')

@section('content')
    @include('partials.titular', ['titular' => __('Password'), 'subtitulo' => ''])

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.password')
    ])

    <div class="card">
        <div class="card-body">
            {!! Form::open(['route' => ['profile.update.password'], 'method' => 'PUT']) !!}

            {{ Form::campoPassword('current', __('Current password')) }}
            {{ Form::campoPassword('password', __('New password')) }}
            {{ Form::campoPassword('password_confirmation', __('Password confirmation')) }}

            @include('partials.guardar')

            @include('layouts.errors')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
