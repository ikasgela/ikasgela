@extends('layouts.app')

@section('content')
    @include('partials.titular', ['titular' => __('Password'), 'subtitulo' => ''])

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.password')
    ])

    <div class="card mb-3">
        <div class="card-body">
            {{ html()->form('PUT', route('profile.update.password'))->open() }}

            @include('components.label-password', [
                'label' => __('Current password'),
                'name' => 'current',
            ])

            @include('components.label-password', [
                'label' => __('New password'),
                'name' => 'password',
            ])

            @include('components.label-password', [
                'label' => __('Password confirmation'),
                'name' => 'password_confirmation',
            ])

            @include('partials.guardar')

            @include('layouts.errors')

            {{ html()->form()->close() }}
        </div>
    </div>
@endsection
