@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Project cloner')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => 'intellij_projects.duplicar']) !!}

            {{ Form::campoTexto('origen', __('Project'), session('intellij_origen', 'programacion.plantillas.proyecto-intellij-java'), ['placeholder' => 'programacion.plantillas.proyecto-intellij-java']) }}
            {{ Form::campoTexto('destino', __('User'), session('intellij_destino'), ['placeholder' => 'marc.ikasgela.com (opcional, por defecto copia en root)']) }}
            {{ Form::campoTexto('ruta', __('New project name'), '', ['placeholder' => 'hola-mundo (opcional, mantiene el original)']) }}
            {{-- Form::campoTexto('nombre', __('New project description'), '', ['placeholder' => 'Hola Mundo (opcional, mantiene el original)']) --}}

            <button type="submit" class="btn btn-primary">{{ __('Clone') }}</button>

            {!! Form::close() !!}

        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('GitLab') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($proyectos as $proyecto)
                <tr>
                    <td>{{ $proyecto['id'] }}</td>
                    <td>{{ $proyecto['name'] }}</td>
                    <td>{{ $proyecto['description'] }}</td>
                    <td>@include('partials.link_gitlab', ['proyecto' => $proyecto ])</td>
                    <td class="text-nowrap">
                        {!! Form::open(['route' => ['intellij_projects.borrar', $proyecto['id']], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            @include('partials.boton_borrar')
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection
