@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Recursos: Proyectos de IntelliJ</h1>
        </div>
    </div>

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('intellij_projects.create') }}">Nuevo proyecto de IntelliJ</a>
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
            @foreach($intellij_projects as $intellij_project)
                <tr>
                    <td>{{ $intellij_project->gitlab()['id'] }}</td>
                    <td>{{ $intellij_project->gitlab()['name'] }}</td>
                    <td>{{ $intellij_project->gitlab()['description'] }}</td>
                    <td>@include('partials.link_gitlab', ['proyecto' => $intellij_project->gitlab() ])</td>
                    <td>
                        <form method="POST" action="{{ route('intellij_projects.destroy', [$intellij_project->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a href="{{ route('intellij_projects.show', [$intellij_project->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                <a href="{{ route('intellij_projects.edit', [$intellij_project->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                <button type="submit" onclick="return confirm('¿Seguro?')"
                                        class="btn btn-light btn-sm"><i class="fas fa-trash text-danger"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
