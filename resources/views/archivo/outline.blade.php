@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Course outline')])

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Todas las actividades del curso.'
    ])

    @if(count($unidades) > 0)

        @foreach($unidades as $unidad)

            @include('partials.subtitulo', ['subtitulo' => (isset($unidad->codigo) ? ($unidad->codigo.' - ') : '') . $unidad->nombre])

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
                            <tr class="table-row" data-href="{{ route('actividades.preview', $actividad->id) }}">
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
        @endforeach

    @else
        <div class="row">
            <div class="col-md-12">
                <p>{{ __('No activities yet.') }}</p>
            </div>
        </div>
    @endif
@endsection
