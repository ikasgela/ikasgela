@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Panel de control</h1>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th></th>
                <th>Nombre</th>
                <th>Asignadas</th>
                <th>Para revisar</th>
                <th>Terminadas</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($usuarios as $usuario)
                <tr class="table-row" data-href="{{ route('profesor.tareas', [$usuario->id]) }}">
                    <td><img style="height:35px;" src="{{ $usuario->avatar_url()}}"/></td>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ count($usuario->actividades_asignadas()) }}</td>
                    <td>{{ count($usuario->actividades_enviadas()) }}</td>
                    <td>{{ count($usuario->actividades_terminadas()) }}</td>
                    <td>
                        <form method="POST"
                              action="{{ route('users.destroy', ['user' => $usuario->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <button type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
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
