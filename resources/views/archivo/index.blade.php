@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Archived')])

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Aquí aparecerán las tareas una vez que las completes.'
    ])

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        {!! Form::open(['route' => ['archivo.alumno'], 'method' => 'POST']) !!}
        @include('partials.desplegable_usuarios')
        {!! Form::close() !!}
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
                    <th>{{ __('Score') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($actividades as $actividad)
                    <tr class="table-row"
                        @if(Auth::user()->hasRole('admin'))
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
                        <td>@include('actividades.partials.puntuacion')</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @include('partials.paginador', ['coleccion' => $actividades])
    @else
        <div class="row">
            <div class="col-md-12">
                <p>No tienes tareas archivadas.</p>
            </div>
        </div>
    @endif
@endsection
