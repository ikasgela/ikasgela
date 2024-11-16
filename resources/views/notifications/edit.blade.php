@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Notification settings'), 'subtitulo' => ''])

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.notificaciones')
    ])

    {{ html()->form('PUT', route('notifications.update'))->open() }}

    @include('partials.subtitulo', ['subtitulo' => __('Tutorship')])

    <div class="card mb-3">
        <div class="card-body pb-0">
            @include('components.label-check', [
                'label' => __('Message received'),
                'name' => 'notificacion_mensaje_recibido',
                'checked' => setting_usuario('notificacion_mensaje_recibido'),
            ])
        </div>
    </div>

    @if(Auth::user()->hasRole('alumno'))

        @include('partials.subtitulo', ['subtitulo' => __('Activities')])

        <div class="card mb-3">
            <div class="card-body pb-0">
                @include('components.label-check', [
                    'label' => __('Activity assigned'),
                    'name' => 'notificacion_actividad_asignada',
                    'checked' => setting_usuario('notificacion_actividad_asignada'),
                ])
                @include('components.label-check', [
                    'label' => __('Feedback received'),
                    'name' => 'notificacion_feedback_recibido',
                    'checked' => setting_usuario('notificacion_feedback_recibido'),
                ])
            </div>
        </div>

    @endif

    @if(Auth::user()->hasRole('profesor'))

        @include('partials.subtitulo', ['subtitulo' => __('Teacher')])

        <div class="card mb-3">
            <div class="card-body pb-0">
                @include('components.label-check', [
                    'label' => __('Task for review'),
                    'name' => 'notificacion_tarea_enviada',
                    'checked' => setting_usuario('notificacion_tarea_enviada'),
                ])
            </div>
        </div>

    @endif

    <div class="mb-3">
        @include('partials.guardar')
        @include('layouts.errors')
    </div>

    {{ html()->form()->close() }}

    @if(Auth::user()->hasRole('admin'))

        @include('partials.subtitulo', ['subtitulo' => __('Notification test')])

        <div class="mb-3">
            <a href="{{ route('notifications.test') }}" class="btn btn-success single_click">
                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Send') }}
            </a>
        </div>
    @endif

@endsection
