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
            @if($curso->minimo_entregadas > 0)
                <td>
                    <table
                        class="tabla-marcador {{ $actividades_obligatorias_superadas ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                        <tr>
                            <th>{{ __('Mandatory activities') }}</th>
                        </tr>
                        <tr>
                            <td class="text-center">{{ $num_actividades_obligatorias > 0 ? $actividades_obligatorias_superadas ? trans_choice('tasks.completed', 2) : ($numero_actividades_completadas+0)."/".($num_actividades_obligatorias+0)  : __('None') }}</td>
                        </tr>
                    </table>
                </td>
            @endif
            @if($minimo_competencias > 0)
                <td>
                    <table
                        class="tabla-marcador {{ $competencias_50_porciento ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                        <tr>
                            <th>{{ __('Skills') }}</th>
                        </tr>
                        <tr>
                            <td class="text-center">{{ $competencias_50_porciento ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) }}</td>
                        </tr>
                    </table>
                </td>
            @endif
            @if($num_pruebas_evaluacion > 0)
                <td>
                    <table
                        class="tabla-marcador {{ $curso->examenes_obligatorios ? $pruebas_evaluacion ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
                        <tr>
                            <th>{{ __('Assessment tests') }}</th>
                        </tr>
                        <tr>
                            <td class="text-center">{{ $num_pruebas_evaluacion > 0 ? $pruebas_evaluacion ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) : __('None') }}</td>
                        </tr>
                    </table>
                </td>
            @endif
            <td>
                <table
                    class="tabla-marcador {{ $evaluacion_continua_superada ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                    <tr>
                        <th>{{ __('Continuous evaluation') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $evaluacion_continua_superada ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table
                    class="tabla-marcador bg-light text-dark">
                    <tr>
                        <th>{{ __('Calification') }}</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{ $evaluacion_continua_superada ? $nota_final : ($curso->disponible() ? __('Unavailable') : __('Fail')) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @include('results.partials.criterios_calificacion')

    <h2>{{ __('Skills development')}}</h2>

    @if(count($skills_curso) > 0)
        <table class="tabla-datos">
            <tr>
                <th class="text-left">{{ __('Skill') }}</th>
                <th>{{ __('Progress') }}</th>
                <th>{{ __('Score') }}</th>
            </tr>
            @foreach ($skills_curso as $skill)

                @php($resultado = $resultados[$skill->id])

                @php($porcentaje_competencia = $resultado->porcentaje_competencia())
                <tr>
                    <td>{{ $skill->name }}</td>
                    <td class="text-center {{ $porcentaje_competencia < $minimo_competencias ? 'bg-warning text-dark' : 'bg-success' }}">
                        {{ formato_decimales($porcentaje_competencia) }}&thinsp;%
                    </td>
                    <td class="text-center">
                        {{ $resultado->tarea }}/{{ $resultado->actividad }}
                    </td>
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

        @include('partials.subtitulo', ['subtitulo' => __('Content development')])

        <table class="tabla-datos">
            <tr>
                <th class="text-left">{{ __('Unit') }}</th>
                <th>{{ __('Progress') }}</th>
                <th>{{ __('Score') }}</th>
            </tr>
            @foreach($unidades as $unidad)

                @php($porcentaje = $resultados_unidades[$unidad->id]->actividad > 0 ? round($resultados_unidades[$unidad->id]->tarea/$resultados_unidades[$unidad->id]->actividad*100) : 0)

                <tr>
                    <td>
                        @isset($unidad->codigo)
                            {{ $unidad->codigo }} -
                        @endisset
                        @include('unidades.partials.nombre_con_etiquetas', ['pdf' => true])
                    </td>
                    <td class="text-center {{ $porcentaje< ($unidad->hasEtiqueta('examen') ? $minimo_examenes : $minimo_competencias) ? 'bg-warning text-dark' : 'bg-success' }}">
                        {{ formato_decimales($porcentaje) }}&thinsp;%
                    </td>
                    <td class="text-center">
                        {{ $resultados_unidades[$unidad->id]->tarea + 0
                     }}/{{ $resultados_unidades[$unidad->id]->actividad + 0 }}
                    </td>
                </tr>
            @endforeach
        </table>

    @else
        <div class="row">
            <div class="col-md-12">
                <p>No hay unidades.</p>
            </div>
        </div>
    @endif

@endsection
