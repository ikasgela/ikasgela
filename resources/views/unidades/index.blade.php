@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Unidades</h1>
        </div>
    </div>

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('unidades.create') }}">Nueva unidad</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>Curso</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Slug</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($unidades as $unidad)
                <tr>
                    <td class="py-3">{{ $unidad->curso->nombre }}</td>
                    <td class="py-3">{{ $unidad->nombre }}</td>
                    <td class="py-3">{{ $unidad->descripcion }}</td>
                    <td class="py-3">{{ $unidad->slug }}</td>
                    <td>
                        <form method="POST" action="{{ route('unidades.destroy', [$unidad->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a href="{{ route('unidades.show', [$unidad->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                <a href="{{ route('unidades.edit', [$unidad->id]) }}"
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
