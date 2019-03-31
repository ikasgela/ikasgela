@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Alumnos</h1>
        </div>
    </div>

    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $usuario)
            <tr>
                <td class="py-3">{{ $usuario->name }}</td>
                <td class="py-3">{{ $usuario->email }}</td>
                <td>
                    <a href="{{ route('tareas.index', [$usuario->id]) }}"
                       class='btn btn-outline-dark'>Asignar actividades</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection
