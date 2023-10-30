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

    @include('partials.subtitulo', ['subtitulo' => __('Your data')])
    <div class="card">
        <div class="card-body">
            <div class="form-group row">
                {!! Form::label('intellij_projects', __('Projects'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    {!! Form::open(['route' => ['intellij_projects.descargar'], 'method' => 'POST']) !!}
                    {!! Form::button(__('Download script'), ['type' => 'submit',
                        'class' => 'btn btn-primary'
                    ]) !!}
                    {!! Form::close() !!}
                    <p class="small m-0 mt-2">{{ __('Click on the button to download the script and run it on your computer. You will need Git installed. On Windows the script can be run from Git Bash.') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.subtitulo', ['subtitulo' => __('Tests')])
        <p>IP: {{ $clientIP }} | Egibide: {{ $ip_egibide ? 'Sí' : 'No' }} | Host: {{ env("HOSTNAME") }}</p>
    @endif
@endsection
