@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Project cloner')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => 'intellij_projects.duplicar']) !!}

            {{ Form::campoTexto('origen', __('Project'), 'programacion/plantillas/proyecto-intellij-java', ['placeholder' => 'programacion/plantillas/proyecto-intellij-java']) }}
            {{ Form::campoTexto('destino', __('Group'), '', ['placeholder' => 'programacion/introduccion (opcional, copia en root/hola-mundo)']) }}
            {{ Form::campoTexto('ruta', __('New project'), '', ['placeholder' => 'hola-mundo']) }}
            {{ Form::campoTexto('nombre', __('New project name'), '', ['placeholder' => 'Hola Mundo (opcional, mantiene el original)']) }}

            <button type="submit" class="btn btn-primary">{{ __('Clone') }}</button>
            <a href="{{ url()->previous() }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>

            {!! Form::close() !!}

        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Repository') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($proyectos as $proyecto)
                <tr>
                    <td class="py-3">{{ $proyecto['id'] }}</td>
                    <td class="py-3">{{ $proyecto['name'] }}</td>
                    <td class="py-3">{{ $proyecto['path_with_namespace'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection
