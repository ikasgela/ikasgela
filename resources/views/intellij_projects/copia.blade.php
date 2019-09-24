@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Project cloner')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => 'intellij_projects.duplicar']) !!}

            {{ Form::campoTexto('origen', __('Project'), session('intellij_origen', 'programacion/plantillas/proyecto-intellij-java'), ['placeholder' => 'programacion/plantillas/proyecto-intellij-java']) }}
            {{ Form::campoTexto('destino', __('Group'), session('intellij_destino'), ['placeholder' => 'programacion/introduccion (opcional, por defecto copia en root)']) }}
            {{ Form::campoTexto('nombre', __('New project name'), '', ['placeholder' => 'Hola Mundo (opcional, mantiene el original)']) }}
            {{ Form::campoTexto('ruta', __('New project slug'), '', ['placeholder' => 'hola-mundo (opcional, lo crea a partir del nombre)']) }}

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
            </tr>
            </thead>
            <tbody>
            @foreach($proyectos as $proyecto)
                <tr class="table-row-blank" data-href="{{ $proyecto['http_url_to_repo'] }}">
                    <td>{{ $proyecto['id'] }}</td>
                    <td>{{ $proyecto['name'] }}</td>
                    <td>{{ $proyecto['description'] }}</td>
                    <td>@include('partials.link_gitlab', ['proyecto' => $proyecto ])</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection
