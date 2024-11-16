@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: IntelliJ projects')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'intellij_projects.index.filtro'])
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('intellij_projects.create') }}">{{ __('New IntelliJ project') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Host') }}</th>
                <th>{{ __('Open with') }}</th>
                <th>{{ __('Repository') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($intellij_projects as $intellij_project)
                <tr>
                    <td>{{ $intellij_project->id }}</td>
                    <td>{{ $intellij_project->titulo }}</td>
                    <td>{{ $intellij_project->descripcion }}</td>
                    <td>{{ $intellij_project->host }}</td>
                    <td>{{ $intellij_project->open_with }}</td>
                    <td>@include('partials.link_gitea', ['proyecto' => $intellij_project->repository() ])</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'intellij_projects', 'recurso' => $intellij_project])
                            @include('partials.boton_editar', ['ruta' => 'intellij_projects', 'recurso' => $intellij_project])
                            @include('partials.boton_duplicar', ['ruta' => 'intellij_projects.duplicar', 'id' => $intellij_project->id, 'middle' => true])
                            {{ html()->form('DELETE', route('intellij_projects.destroy', $intellij_project->id))->open() }}
                            @include('partials.boton_borrar', ['last' => true])
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
