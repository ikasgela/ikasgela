@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Desktop') }}
                @include('partials.boton_recargar')
            </h1>
        </div>
        <div>
            @if(session('num_actividades') > 0)
                @if(session('num_actividades') == 1)
                    <h2 class="text-muted font-xl">Tienes una actividad asignada</h2>
                @else
                    <h2 class="text-muted font-xl">Tienes {{ session('num_actividades') }} actividades asignadas</h2>
                @endif
            @endif
        </div>
    </div>

    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-en-curso-tab" data-toggle="tab" href="#pills-en-curso" role="tab"
               aria-controls="pills-profile" aria-selected="true">En curso
                @if($user->actividades_en_curso_autoavance()->count() > 0)
                    <span class="ml-2 badge badge-danger">{{ $user->actividades_en_curso_autoavance()->count() }}</span>
                @else
                    <span class="ml-2 badge badge-secondary">0</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-enviadas-tab" data-toggle="tab" href="#pills-enviadas" role="tab"
               aria-controls="pills-contact" aria-selected="false">Enviadas
                <span
                    class="ml-2 badge badge-secondary">{{ $user->actividades_enviadas_noautoavance()->count() }}</span>
            </a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tab-content">
        <div class="tab-pane fade show active" id="pills-en-curso" role="tabpanel" aria-labelledby="pills-en-curso-tab">
            @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_en_curso_autoavance()->get(),
            'mensaje_ninguna' => 'No hay actividades en curso.'
            ])
        </div>
        <div class="tab-pane fade" id="pills-enviadas" role="tabpanel" aria-labelledby="pills-enviadas-tab">
            @include('alumnos.partials.panel_actividades', ['actividades' => $user->actividades_enviadas_noautoavance()->get(),
            'mensaje_ninguna' => 'No hay actividades enviadas.'])
        </div>
    </div>
@endsection
