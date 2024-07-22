@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: IntelliJ projects')])

    @include('partials.cabecera_actividad')

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($intellij_projects) > 0 )
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th class="text-center">{{ __('Show title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th class="text-center">{{ __('Show description') }}</th>
                    <th>{{ __('Gitea') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($intellij_projects as $intellij_project)
                    <tr>
                        <td>{{ $intellij_project->id }}</td>
                        <td>{{ $intellij_project->titulo }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'intellij_project',
                                'field' => 'titulo_visible',
                            ])
                        </td>
                        <td>{{ $intellij_project->descripcion }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'intellij_project',
                                'field' => 'descripcion_visible',
                            ])
                        </td>
                        <td>@include('partials.link_gitea', ['proyecto' => $intellij_project->repository() ])</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('intellij_projects.desasociar', ['actividad' => $actividad->id, 'intellij_project' => $intellij_project->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    @include('partials.boton_borrar')
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    @include('partials.subtitulo', ['subtitulo' => __('Available resources')])

    @if(count($disponibles) > 0 )
        <form method="POST" action="{{ route('intellij_projects.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Gitea') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $intellij_project)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $intellij_project->id }}"></td>
                            <td>{{ $intellij_project->id }}</td>
                            <td>{{ $intellij_project->titulo }}</td>
                            <td>{{ $intellij_project->descripcion }}</td>
                            <td>@include('partials.link_gitea', ['proyecto' => $intellij_project->repository() ])</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.paginador', ['coleccion' => $disponibles])

            @include('layouts.errors')

            <div class="mb-4">
                <button type="submit" class="btn btn-primary text-light me-2">{{ __('Save assigment') }}</button>
                <a class="btn btn-secondary"
                   href="{{ route('intellij_projects.create') }}">{{ __('New IntelliJ project') }}</a>
            </div>

        </form>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    <div>
        @include('partials.backbutton')
    </div>
@endsection
