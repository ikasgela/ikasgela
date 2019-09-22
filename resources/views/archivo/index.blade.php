@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Archived')])

    @if(session('tutorial'))
        <div class="callout callout-success b-t-1 b-r-1 b-b-1">
            <small class="text-muted">{{ __('Tutorial') }}</small>
            <p>Aquí aparecerán las tareas una vez que las completes.</p>
        </div>
    @endif
    @if(count($actividades) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>{{ __('Unit') }}</th>
                    <th>{{ __('Name') }}</th>
                    @if(Auth::user()->hasRole('admin'))
                        <th>{{ __('Time spent') }}</th>
                    @endif
                    <th>{{ __('Score') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($actividades as $actividad)
                    <tr class="table-row" data-href="{{ route('archivo.show', $actividad->id) }}">
                        <td class="align-middle">{{ $actividad->unidad->nombre }}</td>
                        <td class="align-middle">
                            @include('actividades.partials.nombre_con_etiquetas')
                        </td>
                        @if(Auth::user()->hasRole('admin'))
                            <td class="align-middle">{{ $actividad->tarea->tiempoDedicado() }}</td>
                        @endif
                        <td>{{ $actividad->tarea->puntuacion + 0 }}/{{ $actividad->puntuacion + 0 }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <p>No tienes tareas archivadas.</p>
            </div>
        </div>
    @endif
@endsection
