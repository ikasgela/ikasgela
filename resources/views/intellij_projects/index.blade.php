@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: IntelliJ projects')])

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
                    <td>@include('partials.link_gitlab', ['proyecto' => $intellij_project->repository() ])</td>
                    <td>
                        <form method="POST" action="{{ route('intellij_projects.destroy', [$intellij_project->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('intellij_projects.edit', [$intellij_project->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                @include('partials.boton_borrar')
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
