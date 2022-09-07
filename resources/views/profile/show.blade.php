@extends('layouts.app')

@section('content')
    @include('partials.titular', ['titular' => __('Profile'), 'subtitulo' => ''])

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => trans('tutorial.perfil')
    ])

    <div class="card">
        <div class="card-body">
            {!! Form::model($user, ['route' => ['profile.update.user'], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('avatar', __('Avatar'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10 col-form-label">
                    <div class="mb-3">
                        @include('users.partials.avatar', ['user' => $user, 'width' => 100])
                    </div>
                    {!! __('You can manage your profile picture on <a href="https://en.gravatar.com/" target="_blank">Gravatar</a>.') !!}
                </div>
            </div>

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('surname', __('Surname')) }}
            {{ Form::campoTextoLabel('email', __('Email')) }}

            @include('partials.guardar')

            @include('layouts.errors')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
