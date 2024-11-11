@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Project cloner')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('intellij_projects.clonar'))->open() }}

            @include('components.label-text', [
                'label' => __('Source'),
                'name' => 'origen',
                'value' => session('intellij_origen'),
                'placeholder' => 'root/programacion.plantillas.proyecto-intellij-java',
            ])
            @include('components.label-text', [
                'label' => __('Destination'),
                'name' => 'destino',
                'value' => session('intellij_destino'),
                'placeholder' => 'root/copia (opcional)',
            ])
            @include('components.label-text', [
                'label' => __('New project description'),
                'name' => 'nombre',
                'placeholder' => 'Hola Mundo (opcional, mantiene el original)',
            ])

            <div class="row mb-3">
                <div class="col-sm-2 d-flex align-items-end">
                    {{ html()->label(__('Create associated resource'), 'recurso_type')->class('form-label') }}
                </div>
                <div class="col-sm-10">
                    {{ html()->select('recurso_type')->class('form-select')->open() }}
                    <option value="-1">{{ __('--- No ---') }}</option>
                    <option value="intellij_project_idea" selected>{{ __('IntelliJ project') }} - IDEA</option>
                    <option value="intellij_project_phpstorm">{{ __('IntelliJ project') }} - PhpStorm</option>
                    <option value="intellij_project_datagrip">{{ __('IntelliJ project') }} - DataGrip</option>
                    <option value="intellij_project">
                        {{ __('IntelliJ project') }} - {{ __('No associated tool') }}
                    </option>
                    <option value="markdown_text">{{ __('Markdown text') }}</option>
                    {{ html()->select()->close() }}
                </div>
            </div>

            {{ html()->submit(__('Clone'))->class('btn btn-primary') }}

            {{ html()->form()->close() }}

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
                        {{ html()->form('DELETE', route('intellij_projects.borrar', $proyecto['id']))->open() }}
                        <div class='btn-group'>
                            @include('partials.boton_borrar')
                        </div>
                        {{ html()->form()->close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection
