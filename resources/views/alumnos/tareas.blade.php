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

    @include('alumnos.partials.safe_exam')

    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
        {{-- Examen --}}
        @if($user->num_actividades_en_curso_examen() > 0)
            <li class="nav-item">
                <a class="nav-link {{ $user->num_actividades_en_curso_examen() > 0 ? 'active' : '' }}"
                   id="pills-examen-tab" data-toggle="tab" href="#pills-examen" role="tab"
                   aria-controls="pills-contact" aria-selected="false">{{ __('Exam') }}
                    <span
                        class="ms-2 badge badge-danger">{{ $user->num_actividades_en_curso_examen() }}</span>
                </a>
            </li>
        @endif
        {{-- En curso --}}
        <li class="nav-item">
            <a class="nav-link {{ $user->num_actividades_en_curso_examen() > 0 ? '' : ($user->num_actividades_en_curso_no_extra_examen() > 0 ? 'active' : '') }}
            {{ $user->num_actividades_en_curso_examen() == 0 && $user->num_actividades_en_curso_extra() == 0 ? 'active':'' }}"
               id="pills-en-curso-tab" data-toggle="tab" href="#pills-en-curso" role="tab"
               aria-controls="pills-profile" aria-selected="true">{{ __('In progress') }}
                <span
                    class="ms-2 badge {{ $user->num_actividades_en_curso_no_extra_examen() > 0 ? 'badge-danger' : 'badge-secondary' }}">{{ $user->num_actividades_en_curso_no_extra_examen() }}</span>
            </a>
        </li>
        {{-- Extra --}}
        @if($user->num_actividades_en_curso_extra() > 0)
            <li class="nav-item">
                <a class="nav-link
                   {{ $user->num_actividades_en_curso_examen() == 0 && $user->num_actividades_en_curso_no_extra_examen() == 0 && $user->num_actividades_en_curso_extra() > 0 ? 'active' : '' }}"
                   id="pills-extra-tab" data-toggle="tab" href="#pills-extra" role="tab"
                   aria-controls="pills-contact" aria-selected="false">{{ __('Extra') }}
                    <span
                        class="ms-2 badge badge-secondary">{{ $user->num_actividades_en_curso_extra() }}</span>
                </a>
            </li>
        @endif
        {{-- Enviadas --}}
        <li class="nav-item">
            <a class="nav-link" id="pills-enviadas-tab" data-toggle="tab" href="#pills-enviadas" role="tab"
               aria-controls="pills-contact" aria-selected="false">{{ trans_choice('tasks.sent', 2) }}
                <span
                    class="ms-2 badge badge-secondary">{{ $user->num_actividades_en_curso_enviadas() }}</span>
            </a>
        </li>
    </ul>
    <div class="tab-content border-bottom border-left border-right" id="pills-tab-content">
        {{-- Examen --}}
        @if($user->num_actividades_en_curso_examen() > 0)
            <div
                class="tab-pane fade {{ $user->num_actividades_en_curso_examen() > 0 ? 'show active' : '' }}"
                id="pills-examen" role="tabpanel" aria-labelledby="pills-examen-tab">
                <div class="p-3">
                    @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_en_curso_examen()->get(),
                    'mensaje_ninguna' => __('There are no exam activities in progress.')
                    ])
                </div>
            </div>
        @endif
        {{-- En curso --}}
        <div
            class="tab-pane fade {{ $user->num_actividades_en_curso_examen() > 0 ? '' : ($user->num_actividades_en_curso_no_extra_examen() > 0 ? 'show active' : '') }}
            {{ $user->num_actividades_en_curso_examen() == 0 && $user->num_actividades_en_curso_extra() == 0 ? 'show active':'' }}"
            id="pills-en-curso" role="tabpanel" aria-labelledby="pills-en-curso-tab">
            <div class="p-3">
                @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_en_curso_no_extra_examen()->get(),
                'mensaje_ninguna' => __('There are no activities in progress.')
                ])
            </div>
        </div>
        {{-- Extra --}}
        @if($user->num_actividades_en_curso_extra() > 0)
            <div
                class="tab-pane fade {{ $user->num_actividades_en_curso_examen() == 0 && $user->num_actividades_en_curso_no_extra_examen() == 0 && $user->num_actividades_en_curso_extra() > 0 ? 'show active' : '' }}"
                id="pills-extra" role="tabpanel" aria-labelledby="pills-extra-tab">
                <div class="p-3">
                    @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_en_curso_extra()->get(),
                    'mensaje_ninguna' => __('There are no extra activities in progress.')
                    ])
                </div>
            </div>
        @endif
        {{-- Enviadas --}}
        <div class="tab-pane fade" id="pills-enviadas" role="tabpanel" aria-labelledby="pills-enviadas-tab">
            <div class="p-3">
                @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_en_curso_enviadas()->get(),
                'mensaje_ninguna' => __('There are no sent activities.')
                ])
            </div>
        </div>
    </div>
@endsection
