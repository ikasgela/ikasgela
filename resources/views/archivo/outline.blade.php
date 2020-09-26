@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Course progress') }}</h1>
        @if(!is_null(Auth::user()->curso_actual()))
            @php($curso = Auth::user()->curso_actual())
            <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
        @endif
    </div>

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Todas las actividades del curso.'
    ])

    @if(count($unidades) > 0)

        @foreach($unidades as $unidad)

            @include('partials.subtitulo', ['subtitulo' => (isset($unidad->codigo) ? ($unidad->codigo.' - ') : '') . $unidad->nombre])

            <div class="pb-3">
                @if(!is_null($unidad->fecha_entrega) && $unidad->fecha_entrega > now())
                    <div class="progress-group">
                        <div class="progress-group-prepend">
                            <span class="progress-group-text">{{ __('Recommended') }}</span>
                        </div>
                        <div class="progress-group-bars">
                            <div class="progress-group-header">
                                <div style="width:10em;">
                                    {{ !is_null($unidad->fecha_disponibilidad) ? $unidad->fecha_disponibilidad->format('d/m/Y H:i') : '-' }}
                                </div>
                                <div class="col text-muted small text-center">
                                    @include('partials.diferencia_fechas', ['fecha_inicial' => now(), 'fecha_final' => $unidad->fecha_entrega])
                                </div>
                                <div class="ml-auto text-right" style="width:10em;">
                                    {{ !is_null($unidad->fecha_entrega) ? $unidad->fecha_entrega->format('d/m/Y H:i') : '-' }}
                                </div>
                            </div>
                            @php($porcentaje = !is_null($unidad->fecha_disponibilidad) && !is_null($unidad->fecha_entrega) ? round(100-(now()->diffInSeconds($unidad->fecha_entrega)/$unidad->fecha_disponibilidad->diffInSeconds($unidad->fecha_entrega)*100),0) : 0)
                            <div class="progress-group-bars">
                                <div class="progress" style="height:24px">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                         style="width: {{ $porcentaje > 0 ? $porcentaje : 0 }}%"
                                         aria-valuenow="{{ $porcentaje > 0 ? $porcentaje : 0 }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $porcentaje > 0 ? $porcentaje : 0 }}&thinsp;%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <div class="progress-group-prepend">
                            <span class="progress-group-text">{{ __('Actual') }}</span>
                        </div>
                        <div class="progress-group-bars">
                            <div class="progress m-0" style="height: 24px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ 60 }}%"
                                     aria-valuenow="{{ 60 }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ formato_decimales(60) }}&thinsp;%
                                </div>
                            </div>
                            <div class="row no-gutters">
                                <div class="col text-muted small" style="flex: 0 0 10%;">0&thinsp;%</div>
                                <div class="col text-muted small text-right pr-1 border-right"
                                     style="flex: 0 0 {{ 50 }}%;">{{ 60 }}&thinsp;%
                                </div>
                                <div class="col text-muted small text-right"
                                     style="flex: 0 0 {{ 40 }}%;">100&thinsp;%
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(!is_null($unidad->fecha_entrega))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                            <tr>
                                <th class="w-75">{{ __('Activity') }}</th>
                                <th>{{ __('Resources') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($unidad->actividades->sortBy('orden') as $actividad)
                                @if($actividad->plantilla && !$actividad->hasEtiqueta('extra') && !$actividad->hasEtiqueta('examen'))
                                    <tr class="table-row"
                                        data-href="{{ route('actividades.preview', $actividad->id) }}">
                                        <td class="align-middle">
                                            @include('actividades.partials.nombre_con_etiquetas')
                                        </td>
                                        <td class="align-middle">
                                            @include('partials.botones_recursos_readonly')
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>{{ __('No dates defined yet.') }}</p>
                @endif
            </div>
        @endforeach

    @else
        <div class="row">
            <div class="col-md-12">
                <p>{{ __('No activities yet.') }}</p>
            </div>
        </div>
    @endif
@endsection
