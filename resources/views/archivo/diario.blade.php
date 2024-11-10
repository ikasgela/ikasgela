@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Activity journal') }}</h1>
        @if(!is_null(Auth::user()->curso_actual()))
            @php($curso = Auth::user()->curso_actual())
            <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
        @endif
    </div>

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.archivo')
    ])

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        <div class="mb-3">
            {{ html()->form('POST', route('archivo.diario'))->open() }}
            @include('partials.desplegable_usuarios')
            {{ html()->form()->close() }}
        </div>
    @endif

    @if(count($actividades) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>{{ __('Unit') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Start date') }}</th>
                    <th>{{ __('Elapsed time') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($actividades as $actividad)
                    <tr class="table-row"
                        @if(Auth::user()->hasAnyRole(['admin', 'profesor', 'tutor']))
                            data-href="{{ route('actividades.preview', $actividad->id) }}">
                        @else
                            data-href="{{ route('archivo.show', $actividad->id) }}">
                        @endif
                        <td class="align-middle">
                            @if($actividad->unidad->id != $actividades[$loop->index-1]?->unidad->id)
                                @isset($actividad->unidad->codigo)
                                    {{ $actividad->unidad->codigo }} -
                                @endisset
                                {{ $actividad->unidad->nombre }}
                            @endif
                        </td>
                        <td class="align-middle">
                            @include('actividades.partials.nombre_con_etiquetas')
                        </td>
                        <td>{{ $actividad->fecha_comienzo->isoFormat('L - LTS') }}</td>
                        <td>
                            @if ($loop->first)
                                <span title="{{ $actividad->fecha_comienzo->isoFormat('L - LTS') }}">-</span>
                            @else
                                {{ $actividad->fecha_comienzo?->diffForHumans($actividades[$loop->index-1]->fecha_comienzo, true) ?: __('Unknown') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.paginador', ['coleccion' => $actividades])
    @else
        <div class="row">
            <div class="col-md-12">
                <p>{{ __('There is not any archived activity.') }}</p>
            </div>
        </div>
    @endif
@endsection
