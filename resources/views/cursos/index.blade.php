@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Cursos</h1>
        </div>
    </div>

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('cursos.create') }}">Nuevo curso</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Slug</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cursos as $curso)
                <tr>
                    <td class="py-3">{{ $curso->nombre }}</td>
                    <td class="py-3">{{ $curso->descripcion }}</td>
                    <td class="py-3">{{ $curso->slug }}</td>
                    <td>
                        <form method="POST" action="{{ route('cursos.destroy', [$curso->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a href="{{ route('cursos.show', [$curso->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                <a href="{{ route('cursos.edit', [$curso->id]) }}"
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
