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

    @include('results.pdf.evaluacion_continua')

    @include('results.partials.criterios_calificacion')

    <h2>{{ __('Skills development')}}</h2>

    @if(count($calificaciones->skills_curso) > 0)
        <table class="tabla-datos">
            <tr>
                <th class="text-left">{{ __('Skill') }}</th>
                <th>{{ __('Activities') }}</th>
                @if($calificaciones->hayExamenes)
                    <th>{{ __('Exams') }}</th>
                @endif
                <th>{{ __('Total') }}</th>
            </tr>
            @foreach ($calificaciones->skills_curso as $skill)

                @php($resultado = $calificaciones->resultados[$skill->id])
                @php($peso_examenes = $skill->peso_examen)

                <tr>
                    <td>{{ $skill->name }}</td>

                    @php($porcentaje_tarea = $resultado->porcentaje_tarea())
                    <td class="text-center">
                        {{ formato_decimales($porcentaje_tarea) }}&thinsp;%
                    </td>
                    @if($peso_examenes>0)
                        @php($porcentaje_examen = $resultado->porcentaje_examen())
                        <td class="text-center">
                            {{ formato_decimales($porcentaje_examen) }}&thinsp;%
                        </td>
                    @elseif($hayExamenes)
                        <td class="text-center">-</td>
                    @endif
                    @php($porcentaje_competencia = $resultado->porcentaje_competencia())
                    <td class="text-center {{ $porcentaje_competencia < $calificaciones->minimo_competencias ? 'bg-warning text-dark' : 'bg-success' }}">
                        {{ formato_decimales($porcentaje_competencia) }}&thinsp;%
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
                    : {{ $calificaciones->numero_actividades_completadas }}
                    - {{ __('Group mean') }}: {{ $media_actividades_grupo }}</th>
            </tr>
        </table>

        @include('partials.subtitulo', ['subtitulo' => __('Content development')])

        <table class="tabla-datos">
            <tr>
                <th class="text-left">{{ __('Unit') }}</th>
                <th>{{ __('Score') }}</th>
                <th>{{ __('Total') }}</th>
            </tr>
            @foreach($unidades as $unidad)

                @php($porcentaje = $calificaciones->resultados_unidades[$unidad->id]->actividad > 0 ? round($calificaciones->resultados_unidades[$unidad->id]->tarea/$calificaciones->resultados_unidades[$unidad->id]->actividad*100) : 0)

                <tr>
                    <td>
                        @isset($unidad->codigo)
                            {{ $unidad->codigo }} -
                        @endisset
                        @include('unidades.partials.nombre_con_etiquetas', ['pdf' => true])
                    </td>
                    <td class="text-center">
                        {{ $calificaciones->resultados_unidades[$unidad->id]->tarea + 0
                     }}/{{ $calificaciones->resultados_unidades[$unidad->id]->actividad + 0 }}
                    </td>
                    <td class="text-center {{ $porcentaje< ($unidad->hasEtiqueta('examen') ? $calificaciones->minimo_examenes : $calificaciones->minimo_competencias) ? 'bg-warning text-dark' : 'bg-success' }}">
                        {{ formato_decimales($porcentaje) }}&thinsp;%
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
