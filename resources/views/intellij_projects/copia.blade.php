@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Project cloner')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => 'intellij_projects.duplicar']) !!}

            {{ Form::campoTexto('origen', __('Source'), session('intellij_origen', 'root/programacion.plantillas.proyecto-intellij-java'), ['placeholder' => 'root/programacion.plantillas.proyecto-intellij-java']) }}
            {{ Form::campoTexto('destino', __('Destination'), session('intellij_destino'), ['placeholder' => 'root/copia (opcional)']) }}
            {{ Form::campoTexto('nombre', __('New project description'), '', ['placeholder' => 'Hola Mundo (opcional, mantiene el original)']) }}

            <div class="form-group row">
                {!! Form::label('recurso_type', __('Create associated resource'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="recurso_type" name="recurso_type">
                        <option value="-1">{{ __('--- No ---') }}</option>
                        <option value="intellij_project_idea" selected>{{ __('IntelliJ project') }} - IDEA</option>
                        <option value="intellij_project_phpstorm">{{ __('IntelliJ project') }} - PhpStorm</option>
                        <option value="intellij_project_datagrip">{{ __('IntelliJ project') }} - DataGrip</option>
                        <option value="intellij_project">
                            {{ __('IntelliJ project') }} - {{ __('No associated tool') }}
                        </option>
                        <option value="markdown_text">{{ __('Markdown text') }}</option>
                    </select>
                </div>
            </div>

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
                <th>{{ __('Repository') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($proyectos as $proyecto)
                <tr>
                    <td>{{ $proyecto['id'] }}</td>
                    <td>{{ $proyecto['full_name'] }}</td>
                    <td>{{ $proyecto['description'] }}</td>
                    <td>@include('partials.link_gitea', ['proyecto' => $proyecto ])</td>
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
