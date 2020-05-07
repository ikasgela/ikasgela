@extends('layouts.app')

@include('partials.prismjs')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Desktop') }}
                @include('partials.boton_recargar')
            </h1>
        </div>
        <div>
            @if($alumno_actividades_asignadas > 0)
                @if($alumno_actividades_asignadas == 1)
                    <h2 class="text-muted font-xl">Tienes una actividad asignada</h2>
                @else
                    <h2 class="text-muted font-xl">Tienes {{ $alumno_actividades_asignadas }} actividades asignadas</h2>
                @endif
            @endif
        </div>
    </div>

    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-en-curso-tab" data-toggle="tab" href="#pills-en-curso" role="tab"
               aria-controls="pills-profile" aria-selected="true">{{ __('In progress') }}
                @if($alumno_actividades_asignadas > 0)
                    <span class="ml-2 badge badge-danger">{{ $alumno_actividades_asignadas }}</span>
                @else
                    <span class="ml-2 badge badge-secondary">0</span>
                @endif
            </a>
        </li>
        @if($user->actividades()->tag('extra')->count() > 0)
            <li class="nav-item">
                <a class="nav-link" id="pills-extra-tab" data-toggle="tab" href="#pills-extra" role="tab"
                   aria-controls="pills-contact" aria-selected="false">{{ __('Extra') }}
                    <span
                        class="ml-2 badge badge-secondary">{{ $user->actividades_en_curso_autoavance()->tag('extra')->count() }}</span>
                </a>
            </li>
        @endif
        <li class="nav-item">
            <a class="nav-link" id="pills-enviadas-tab" data-toggle="tab" href="#pills-enviadas" role="tab"
               aria-controls="pills-contact" aria-selected="false">{{ trans_choice('tasks.sent', 2) }}
                <span
                    class="ml-2 badge badge-secondary">{{ $user->actividades_enviadas_noautoavance()->count() }}</span>
            </a>
        </li>
    </ul>
    <div class="tab-content border-bottom border-left border-right" id="pills-tab-content">
        <div class="tab-pane fade show active" id="pills-en-curso" role="tabpanel" aria-labelledby="pills-en-curso-tab">
            <div class="p-3">
                @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_en_curso_autoavance()->tag('extra', false)->get(),
                'mensaje_ninguna' => 'No hay actividades en curso.'
                ])
            </div>
        </div>
        @if($user->actividades()->tag('extra')->count() > 0)
            <div class="tab-pane fade" id="pills-extra" role="tabpanel" aria-labelledby="pills-extra-tab">
                <div class="p-3">
                    @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_en_curso_autoavance()->tag('extra')->get(),
                    'mensaje_ninguna' => 'No hay actividades extra en curso.'
                    ])
                </div>
            </div>
        @endif
        <div class="tab-pane fade" id="pills-enviadas" role="tabpanel" aria-labelledby="pills-enviadas-tab">
            <div class="p-3">
                @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_enviadas_noautoavance()->get(),
                'mensaje_ninguna' => 'No hay actividades enviadas.'])
            </div>
        </div>
    </div>
@endsection
