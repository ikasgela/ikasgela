@extends('layouts.app')

@section('content')

    @if(!is_null($curso))
        @include('partials.titular', ['titular' => __('Results'), 'subtitulo' => $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre  ?? '' ])
    @else
        @include('partials.titular', ['titular' => __('Results')])
    @endif

    @if(session('tutorial'))
        <div class="callout callout-success b-t-1 b-r-1 b-b-1">
            <small class="text-muted">{{ __('Tutorial') }}</small>
            <p>Aquí aparecerán los resultados de las competencias asociadas al curso.</p>
        </div>
    @endif

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST']) !!}
        @include('partials.desplegable_usuarios')
        {!! Form::close() !!}
    @endif

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
                    <th class="text-center">Repaso</th>
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

    <div>
        {!! $chart->container() !!}
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    {!! $chart->script() !!}
@endsection
