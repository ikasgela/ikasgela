@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: IntelliJ projects')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['intellij_projects.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
    @endif

    <div class="mb-3">
        <a class="btn btn-primary text-light" href="{{ route('intellij_projects.create') }}">{{ __('New IntelliJ project') }}</a>
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
                            {!! Form::open(['route' => ['intellij_projects.duplicar', $intellij_project->id], 'method' => 'POST']) !!}
                            @include('partials.boton_duplicar')
                            {!! Form::close() !!}
                            {!! Form::open(['route' => ['intellij_projects.destroy', $intellij_project->id], 'method' => 'DELETE']) !!}
                            @include('partials.boton_borrar')
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
