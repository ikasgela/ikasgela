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
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                        <tr class="border-secondary">
                            <th class="bg-secondary text-dark w-25">{{ __('Availability date') }}</th>
                            <td class="align-middle">{{ !is_null($unidad->fecha_disponibilidad) ? $unidad->fecha_disponibilidad->format('d/m/Y H:i:s') : '-' }}</td>
                            <th class="bg-secondary text-dark w-25">{{ __('Due date') }}</th>
                            <td class="align-middle">{{ !is_null($unidad->fecha_entrega) ? $unidad->fecha_entrega->format('d/m/Y H:i:s') : '-' }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                @if(!is_null($unidad->fecha_entrega) && $unidad->fecha_entrega < now())
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
