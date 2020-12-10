@extends('layouts.pdf')

@section('content')

    <h1>{{ __('Results') }}</h1>

    <table class="tabla-datos border-dark">
        <tr>
            <th style="width:4cm;">{{ __('Course') }}</th>
            <td>{{ $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre  ?? '' }}</td>
        </tr>
        <tr>
            <th>{{ __('Name') }}</th>
            <td>{{ $user->name }} {{ $user->surname }}</td>
        </tr>
        <tr>
            <th>{{ __('Date') }}</th>
            <td>{{ now()->isoFormat('LLLL') }}</td>
        </tr>
    </table>

    <h2>{{ __('Continuous evaluation') }}</h2>

    <table class="tabla-marcador-contenedor">
        <tr>
            <td>
                <table
                    class="tabla-marcador {{ $num_actividades_obligatorias > 0 ? $actividades_obligatorias_superadas ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
                    <tr>
                        <th>{{ __('Mandatory activities') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $num_actividades_obligatorias > 0 ? $actividades_obligatorias_superadas ? trans_choice('tasks.completed', 2) : ($numero_actividades_completadas+0)."/".($num_actividades_obligatorias+0)  : __('None') }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table
                    class="tabla-marcador {{ $num_pruebas_evaluacion > 0 ? $pruebas_evaluacion ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
                    <tr>
                        <th>{{ __('Assessment tests') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $num_pruebas_evaluacion > 0 ? $pruebas_evaluacion ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) : __('None') }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table
                    class="tabla-marcador {{ ($actividades_obligatorias_superadas || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento && $nota_final >= 5 ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                    <tr>
                        <th>{{ __('Continuous evaluation') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ ($actividades_obligatorias_superadas || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento && $nota_final >= 5 ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table
                    class="tabla-marcador {{ ($actividades_obligatorias_superadas || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento ? $nota_final >= 5 ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
                    <tr>
                        <th>{{ __('Calification') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $competencias_50_porciento ? $nota_final : __('Unavailable') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <h2>{{ __('Skills development')}}</h2>

    @if(count($skills_curso) > 0)
        <table class="tabla-datos">
            <tr>
                <th class="text-left">{{ __('Skill') }}</th>
                <th>{{ __('Progress') }}</th>
                <th>{{ __('Score') }}</th>
            </tr>
            @foreach ($skills_curso as $skill)
                @php($porcentaje = $resultados[$skill->id]->actividad > 0 ? round($resultados[$skill->id]->tarea/$resultados[$skill->id]->actividad*100) : 0)
                <tr>
                    <td>{{ $skill->name }}</td>
                    <td class="text-center {{ $porcentaje > 0 && $porcentaje<50 ? 'bg-warning text-dark' : ($porcentaje >= 50 ? 'bg-success' : '') }}">
                        {{ $porcentaje }}&thinsp;%
                    </td>
                    <td class="text-center">
                        {{ $resultados[$skill->id]->tarea + 0 }}/{{ $resultados[$skill->id]->actividad + 0 }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p>{{ __('No skills assigned.') }}</p>
    @endif

    <h2>{{ __('Completed activities') }}</h2>

    @if($unidades->count() > 0)
        <table class="tabla-datos">
            <tr>
                <th class="text-left">{{ __('Unit') }}</th>
                <th class="text-center">{{ __('Base') }}</th>
                <th class="text-center">{{ __('Extra') }}</th>
                <th class="text-center">{{ __('Revisit') }}</th>
            </tr>
            @foreach($unidades as $unidad)
                @if(!$unidad->hasEtiqueta('examen'))
                    <tr>
                        <td>
                            @isset($unidad->codigo)
                                {{ $unidad->codigo }} -
                            @endisset
                            @include('unidades.partials.nombre_con_etiquetas')
                        </td>
                        <td class="text-center {{ $unidad->num_actividades('base') > 0 ? $user->num_completadas('base', $unidad->id) < $unidad->num_actividades('base') ? 'bg-warning text-dark' : 'bg-success' : '' }}">
                            {{ $user->num_completadas('base', $unidad->id).'/'. $unidad->num_actividades('base') }}
                        </td>
                        <td class="text-center">
                            {{ $user->num_completadas('extra', $unidad->id) }}
                        </td>
                        <td class="text-center">
                            {{ $user->num_completadas('repaso', $unidad->id) }}
                        </td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th colspan="4" class="text-left">{{ __('Completed activities') }}
                    : {{ $numero_actividades_completadas }}
                    - {{ __('Group mean') }}: {{ $media_actividades_grupo }}</th>
            </tr>
        </table>

        {{--
                @include('partials.subtitulo', ['subtitulo' => __('Content development')])

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
                --}}

    @else
        <div class="row">
            <div class="col-md-12">
                <p>No hay unidades.</p>
            </div>
        </div>
    @endif

@endsection
