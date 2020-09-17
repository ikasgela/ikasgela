@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Results') }}
            @if(!is_null($user->curso_actual()))

            @if(!Auth::user()->hasAnyRole(['profesor', 'tutor']))
                <a class="ml-3"
                   style="color:#ed2224" {{-- https://www.schemecolor.com/adobe-inc-logo-colors.php --}}
                   title="{{ __('Export to PDF') }}"
                   href="{{ route('results.pdf') }}"><i class="fas fa-file-pdf"></i>
                </a>
            @else
                {!! Form::open(['route' => ['results.pdf'], 'method' => 'POST', 'class'=>'d-inline']) !!}
                {!! Form::button('<i class="fas fa-file-pdf"></i>', [
                    'type' => 'submit',
                    'class'=>'btn btn-link',
                    'style'=>'color:#ed2224; font-size:inherit; display:inline; padding-top:0;',
                ]) !!}
                {!! Form::hidden('user_id',request()->user_id) !!}
                {!! Form::close() !!}
            @endif
            @endif
        </h1>
        <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
    </div>

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Aquí aparecerán los resultados de las competencias asociadas al curso.'
    ])

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST']) !!}
        @include('partials.desplegable_usuarios')
        {!! Form::close() !!}
    @endif

    @if(!is_null($user->curso_actual()))
    @include('partials.subtitulo', ['subtitulo' => __('Continuous evaluation')])

    <div class="card-deck">
        <div
            class="card mb-3 {{ $num_actividades_obligatorias > 0 ? $actividades_obligatorias ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
            <div
                class="card-header">{{ __('Mandatory activities') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">{{ $num_actividades_obligatorias > 0 ? $actividades_obligatorias ? trans_choice('tasks.completed', 2) : ($numero_actividades_completadas+0)."/".($num_actividades_obligatorias+0)  : __('None') }}</p>
            </div>
        </div>
        <div
            class="card mb-3 {{ $num_pruebas_evaluacion > 0 ? $pruebas_evaluacion ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
            <div class="card-header">{{ __('Assessment tests') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">
                    {{ $num_pruebas_evaluacion > 0 ? $pruebas_evaluacion ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) : __('None') }}</p>
            </div>
        </div>
        <div
            class="card mb-3 {{ ($actividades_obligatorias || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento ? $nota_final >= 5 ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
            <div class="card-header">{{ __('Calification') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">{{ $competencias_50_porciento ? $nota_final : __('Unavailable') }}</p>
            </div>
        </div>
        <div
            class="card mb-3 {{ ($actividades_obligatorias || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento && $nota_final >= 5 ? 'bg-success text-white' : 'bg-warning text-dark' }}">
            <div class="card-header">{{ __('Continuous evaluation') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">{{ ($actividades_obligatorias || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento && $nota_final >= 5 ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</p>
            </div>
        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Skills development')])

    @if(count($skills_curso) > 0)
        {{-- Tarjeta --}}
        <div class="card">
            <div class="card-body">
                @foreach ($skills_curso as $skill)
                    <h5 class="card-title">{{ $skill->name }}</h5>
                    <p class="ml-5">{{ $skill->description }}</p>

                    <div class="row no-gutters ml-5">
                        <div class="col" style="flex: 0 0 60%;">Actividades</div>
                        <div style="flex: 0 0 40%;">Exámenes</div>
                    </div>

                    <div class="progress ml-5" style="height: 24px;">
                        <div class="progress-bar" role="progressbar" style="width: 9%" aria-valuenow="15"
                             aria-valuemin="0" aria-valuemax="100">15&thinsp;%
                        </div>
                        <div class="progress-bar bg-gray-200" style="width: 51%"></div>

                        <div class="progress-bar" role="progressbar" style="width: 12%"
                             aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">30&thinsp;%
                        </div>
                        <div class="progress-bar bg-gray-200" style="width: 28%"></div>
                    </div>

                    <div class="row no-gutters ml-5">
                        <div class="col text-muted small text-right" style="flex: 0 0 60%;">60&thinsp;%</div>
                        <div class="col text-muted small text-right" style="flex: 0 0 40%;">40&thinsp;%</div>
                    </div>

                    <div class="row no-gutters ml-5">
                        <div class="col" style="flex: 0 0 60%;">
                            <span>Total de la competencia</span>
                        </div>
                    </div>

                    <div class="ml-5 progress" style="height: 24px;">
                        @php($porcentaje = $resultados[$skill->id]->actividad > 0 ? round($resultados[$skill->id]->tarea/$resultados[$skill->id]->actividad*100) : 0)
                        <div class="progress-bar {{ $porcentaje<50 ? 'bg-warning text-dark' : 'bg-success' }}"
                             role="progressbar"
                             style="width: {{ $porcentaje }}%;"
                             aria-valuenow="{{ $porcentaje }}"
                             aria-valuemin="0"
                             aria-valuemax="100">@if($porcentaje>0){{ $porcentaje }}&nbsp;%@endif
                        </div>
                    </div>
                    <div class="text-muted small text-right">
                        {{ $resultados[$skill->id]->tarea + 0 }}/{{ $resultados[$skill->id]->actividad + 0 }}
                    </div>
                    @if(!$loop->last)
                        <hr>
                    @endif
                @endforeach
            </div>
        </div>
        {{-- Fin tarjeta--}}
    @else
        <p>{{ __('No skills assigned.') }}</p>
    @endif

    @include('partials.subtitulo', ['subtitulo' => __('Completed activities')])

    @if($unidades->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>{{ __('Unit') }}</th>
                    <th class="text-center">{{ __('Base') }}</th>
                    <th class="text-center">{{ __('Extra') }}</th>
                    <th class="text-center">{{ __('Revisit') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($unidades as $unidad)
                    @if(!$unidad->hasEtiqueta('examen'))
                        <tr>
                            <td class="align-middle">
                                @isset($unidad->codigo)
                                    {{ $unidad->codigo }} -
                                @endisset
                                @include('unidades.partials.nombre_con_etiquetas')
                            </td>
                            <td class="align-middle text-center {{ $unidad->num_actividades('base') > 0 ? $user->num_completadas('base', $unidad->id) < $unidad->num_actividades('base') ? 'bg-warning text-dark' : 'bg-success' : '' }}">
                                {{ $user->num_completadas('base', $unidad->id).'/'. $unidad->num_actividades('base') }}
                            </td>
                            <td class="align-middle text-center">
                                {{ $user->num_completadas('extra', $unidad->id) }}
                            </td>
                            <td class="align-middle text-center">
                                {{ $user->num_completadas('repaso', $unidad->id) }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
                <tfoot class="thead-dark">
                <tr>
                    <th colspan="4">{{ __('Completed activities') }}: {{ $numero_actividades_completadas }}
                        - {{ __('Group mean') }}: {{ $media_actividades_grupo }}</th>
                </tr>
                </tfoot>
            </table>
        </div>

        @include('partials.subtitulo', ['subtitulo' => __('Content development')])

        {{-- Tarjeta --}}
        <div class="card">
            <div class="card-body">
                @foreach ($unidades as $unidad)
                    <h5 class="card-title">
                        @isset($unidad->codigo)
                            {{ $unidad->codigo }} -
                        @endisset
                        @include('unidades.partials.nombre_con_etiquetas')
                    </h5>
                    <p class="ml-5">{{ $unidad->descripcion }}</p>
                    <div class="ml-5 progress" style="height: 24px;">
                        @php($porcentaje = $resultados_unidades[$unidad->id]->actividad > 0 ? round($resultados_unidades[$unidad->id]->tarea/$resultados_unidades[$unidad->id]->actividad*100) : 0)
                        <div class="progress-bar {{ $porcentaje<50 ? 'bg-warning text-dark' : 'bg-success' }}"
                             role="progressbar"
                             style="width: {{ $porcentaje }}%;"
                             aria-valuenow="{{ $porcentaje }}"
                             aria-valuemin="0"
                             aria-valuemax="100">@if($porcentaje>0){{ $porcentaje }}&nbsp;%@endif
                        </div>
                    </div>
                    <div class="text-muted small text-right">
                        {{ $resultados_unidades[$unidad->id]->tarea + 0
                        }}/{{ $resultados_unidades[$unidad->id]->actividad + 0 }}
                    </div>
                    @if(!$loop->last)
                        <hr>
                    @endif
                @endforeach
            </div>
        </div>
        {{-- Fin tarjeta--}}

    @else
        <div class="row">
            <div class="col-md-12">
                <p>No hay unidades.</p>
            </div>
        </div>
    @endif

    @include('partials.subtitulo', ['subtitulo' => __('Activities per day')])

    @include('partials.grafico_enviadas')

    @else
        <div class="row">
            <div class="col-md-12">
                <p>{{ __("There's no data to show.") }}</p>
            </div>
        </div>
    @endif

@endsection
