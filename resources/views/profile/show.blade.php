@extends('layouts.app')

@section('content')
    @include('partials.titular', ['titular' => __('Profile'), 'subtitulo' => ''])

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.perfil')
    ])

    <div class="card mb-3">
        <div class="card-body">
            {{ html()->modelForm($user, 'PUT', route('profile.update.user', $user->id))->open() }}

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])

            @include('components.label-text', [
                'label' => __('Surname'),
                'name' => 'surname',
            ])

            @include('components.label-text-readonly', [
                'label' => __('Email'),
                'name' => 'email',
            ])

            <div class="row mb-3">
                <div class="col-sm-2 d-flex align-items-center">
                    {{ html()->label(__('Avatar'), 'avatar')->class('form-label') }}
                </div>
                <div class="col-sm-10">
                    <div class="mb-3">
                        @include('users.partials.avatar', ['user' => $user, 'width' => 100])
                    </div>
                    {!! __('You can manage your profile picture on <a href="https://gravatar.com" target="_blank">Gravatar</a>.') !!}
                </div>
            </div>

            @include('components.label-text', [
                'label' => __('Email for Gravatar'),
                'name' => 'gravatar_email',
            ])

            @include('partials.guardar')

            @include('layouts.errors')

            {{ html()->closeModelForm() }}
        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Your data')])
    <div class="card mb-3">
        <div class="card-body pb-0">
            {{ html()->form('POST', route('intellij_projects.descargar'))->open() }}
            <div class="row mb-3">
                <div class="col-sm-2 d-flex align-items-center">
                    {{ html()->label(__('Projects'), 'intellij_projects')->class('form-label') }}
                </div>
                <div class="col-sm-10">
                    {{ html()->submit(__('Download script'))->class('btn btn-primary') }}
                    <p class="small m-0 mt-2">{{ __('Click on the button to download the script and run it on your computer. You will need Git installed. On Windows the script can be run from Git Bash.') }}</p>
                </div>
            </div>
            {{ html()->form()->close() }}
        </div>
    </div>

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.subtitulo', ['subtitulo' => __('Tests')])
        <p>IP: {{ $clientIP }} | Egibide: {{ $ip_egibide ? 'Sí' : 'No' }}</p>
        <p>Host: {{ env("HOSTNAME") }}</p>
        <p>Token válido: {{ $user->curso_actual()?->token_valido() ? "Sí" : "No" }}</p>
    @endif
@endsection
