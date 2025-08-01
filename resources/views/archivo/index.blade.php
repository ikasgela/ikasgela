@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Archived') }}</h1>
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
            {{ html()->form('POST', route('archivo.alumno'))->open() }}
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
                    <th>{{ __('Resources') }}</th>
                    @if(Auth::user()->hasRole('admin'))
                        <th class="text-center">{{ __('Attempts') }}</th>
                        <th>{{ __('Time spent') }}</th>
                    @endif
                    @if($curso?->mostrar_calificaciones)
                        <th>{{ __('Score') }}</th>
                    @endif
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
                            @isset($actividad->unidad->codigo)
                                {{ $actividad->unidad->codigo }} -
                            @endisset
                            {{ $actividad->unidad->nombre }}
                        </td>
                        <td class="align-middle">
                            @include('actividades.partials.nombre_con_etiquetas')
                        </td>
                        <td class="align-middle">
                            @include('partials.botones_recursos_readonly')
                        </td>
                        @if(Auth::user()->hasRole('admin'))
                            <td class="align-middle text-center">{{ $actividad->tarea->intentos }}</td>
                            <td class="align-middle">{{ $actividad->tarea->tiempoDedicado() }}</td>
                        @endif
                        @if($curso?->mostrar_calificaciones)
                            <td>@include('actividades.partials.puntuacion')</td>
                        @endif
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
