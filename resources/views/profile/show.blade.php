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
                <div class="col-sm-2">
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

    @if($cursos_finalizados > 0 || Auth::user()->hasAnyRole(['admin']))
        @livewire('exportar-usuario', ['user' => $user])
    @endif

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.subtitulo', ['subtitulo' => __('Tests')])
        <p>IP: {{ $clientIP }} | Egibide: {{ $ip_egibide ? 'Sí' : 'No' }}</p>
        <p>Host: {{ env("HOSTNAME") }}</p>
        <p>Token válido: {{ $user->curso_actual()?->token_valido() ? "Sí" : "No" }}</p>
    @endif
@endsection
