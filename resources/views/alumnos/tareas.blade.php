@extends('layouts.app')

@include('partials.prismjs')

@include('partials.reload_position')

@section('content')

    <div class="d-flex justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Desktop') }}
                @include('partials.boton_recargar')
            </h1>
        </div>
        <div>
            @if($alumno_actividades_asignadas > 0)
                @if($alumno_actividades_asignadas == 1)
                    <h2 class="text-muted">{{ __('You have one assigned activity') }}</h2>
                @else
                    <h2 class="text-muted">{{ __('You have :count assigned activities', ['count' => $alumno_actividades_asignadas]) }}</h2>
                @endif
            @elseif(is_null(Auth::user()->curso_actual()))
                <h2><a class="text-danger" href="{{ route('users.portada') }}">{{ __('No course selected') }}</a></h2>
            @endif
        </div>
    </div>

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.desktop')
    ])

    @include('alumnos.partials.safe_exam')

    <ul class="nav nav-tabs mb-3" id="tab" role="tablist">
        {{-- Examen --}}
        @if($user->num_actividades_en_curso_examen() > 0)
            <li class="nav-item" role="presentation">
                @php
                    $is_examen_active = $user->num_actividades_en_curso_examen() > 0;
                @endphp
                <button class="nav-link {{ $is_examen_active ? 'active' : '' }} d-flex align-items-center"
                        id="examen-tab" data-bs-target="#examen-tab-pane" aria-controls="examen-tab-pane"
                        data-bs-toggle="tab" type="button" role="tab"
                        aria-selected="{{ $is_examen_active ? 'true' : 'false' }}">
                    <span>{{ __('Exam') }}</span>
                    <span class="ms-2 badge text-bg-danger fw-light">
                        {{ $user->num_actividades_en_curso_examen() }}
                    </span>
                </button>
            </li>
        @endif
        {{-- En curso --}}
        <li class="nav-item" role="presentation">
            @php
                $is_en_curso_active = !($user->num_actividades_en_curso_examen() > 0) && $user->num_actividades_en_curso_no_extra_examen() > 0
                                        || $user->num_actividades_en_curso_examen() == 0 && $user->num_actividades_en_curso_extra() == 0;
            @endphp
            <button class="nav-link {{ $is_en_curso_active ? 'active' : '' }} d-flex align-items-center"
                    id="en-curso-tab" data-bs-target="#en-curso-tab-pane" aria-controls="en-curso-tab-pane"
                    data-bs-toggle="tab" type="button" role="tab"
                    aria-selected="{{ $is_en_curso_active ? 'true' : 'false' }}">
                <span>{{ __('In progress') }}</span>
                @php
                    $badge_style = $user->num_actividades_en_curso_no_extra_examen() > 0 ? 'text-bg-danger' : 'text-bg-secondary';
                @endphp
                <span class="ms-2 badge {{ $badge_style }} fw-light">
                    {{ $user->num_actividades_en_curso_no_extra_examen() }}
                </span>
            </button>
        </li>
        {{-- Extra --}}
        @if($user->num_actividades_en_curso_extra() > 0)
            <li class="nav-item" role="presentation">
                @php
                    $is_extra_active = $user->num_actividades_en_curso_examen() == 0
                                        && $user->num_actividades_en_curso_no_extra_examen() == 0
                                        && $user->num_actividades_en_curso_extra() > 0;
                @endphp
                <button class="nav-link {{ $is_extra_active ? 'active' : '' }} d-flex align-items-center"
                        id="extra-tab" data-bs-target="#extra-tab-pane" aria-controls="extra-tab-pane"
                        data-bs-toggle="tab" type="button" role="tab"
                        aria-selected="{{ $is_extra_active ? 'true' : 'false' }}">
                    <span>{{ __('Extra') }}</span>
                    <span class="ms-2 badge text-bg-secondary fw-light">
                        {{ $user->num_actividades_en_curso_extra() }}
                    </span>
                </button>
            </li>
        @endif
        {{-- Enviadas --}}
        @if($user->curso_actual()?->mostrar_calificaciones)
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center"
                        id="enviadas-tab" data-bs-target="#enviadas-tab-pane" aria-controls="enviadas-tab-pane"
                        data-bs-toggle="tab" type="button" role="tab"
                        aria-selected="false">
                    <span>{{ trans_choice('tasks.sent', 2) }}</span>
                    <span class="ms-2 badge text-bg-secondary fw-light">
                        {{ $user->num_actividades_en_curso_enviadas() }}
                    </span>
                </button>
            </li>
        @endif
    </ul>
    <div class="tab-content" id="tab-content">
        {{-- Examen --}}
        @if($user->num_actividades_en_curso_examen() > 0)
            <div class="tab-pane fade {{ $is_examen_active ? 'show active' : '' }}"
                 id="examen-tab-pane" aria-labelledby="examen-tab"
                 role="tabpanel">
                @include('alumnos.partials.panel_actividades', [
                    'actividades' => $user->actividades_en_curso_examen()->get(),
                    'mensaje_ninguna' => __('There are no exam activities in progress.')
                ])
            </div>
        @endif
        {{-- En curso --}}
        <div class="tab-pane fade {{ $is_en_curso_active ? 'show active' : '' }}"
             id="en-curso-tab-pane" aria-labelledby="en-curso-tab"
             role="tabpanel">
            @include('alumnos.partials.panel_actividades', [
                'actividades' => $user->actividades_en_curso_no_extra_examen()->get(),
                'mensaje_ninguna' => __('There are no activities in progress.')
            ])
        </div>
        {{-- Extra --}}
        @if($user->num_actividades_en_curso_extra() > 0)
            <div class="tab-pane fade {{ $is_extra_active ? 'show active' : '' }}"
                 id="extra-tab-pane" aria-labelledby="extra-tab"
                 role="tabpanel">
                @include('alumnos.partials.panel_actividades', [
                    'actividades' => $user->actividades_en_curso_extra()->get(),
                    'mensaje_ninguna' => __('There are no extra activities in progress.')
                ])
            </div>
        @endif
        {{-- Enviadas --}}
        @if($user->curso_actual()?->mostrar_calificaciones)
            <div class="tab-pane fade"
                 id="enviadas-tab-pane" aria-labelledby="enviadas-tab"
                 role="tabpanel">
                @include('alumnos.partials.panel_actividades', [
                    'actividades' => $user->actividades_en_curso_enviadas()->get(),
                    'mensaje_ninguna' => __('There are no sent activities.')
                ])
            </div>
        @endif
    </div>
@endsection
