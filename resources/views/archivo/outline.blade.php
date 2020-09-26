@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Course outline') }}</h1>
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

            <div class="ml-4">
                @if(!is_null($unidad->fecha_entrega) && $unidad->fecha_entrega > now())
                    <div class="progress-group">
                        <div class="progress-group-prepend">
                            <span class="progress-group-text">{{ __('Recommended progress') }}</span>
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
                                    <div class="progress-bar bg-warning text-dark" role="progressbar"
                                         style="width: {{ $porcentaje }}%"
                                         aria-valuenow="{{ $porcentaje }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $porcentaje }}&thinsp;%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(!is_null($unidad->fecha_entrega))
                    <h3>{{ __('Activities') }}</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                            <tr>
                                <th class="w-75">{{ __('Name') }}</th>
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
