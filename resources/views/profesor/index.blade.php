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
                <th class="text-center">Nuevas</th>
                <th class="text-center">En curso</th>
                <th class="text-center">Enviadas</th>
                <th class="text-center">Terminadas</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($usuarios as $usuario)
                <tr class="table-row" data-href="{{ route('profesor.tareas', [$usuario->id]) }}">
                    <td class="align-middle"><img style="height:35px;" src="{{ $usuario->avatar_url()}}"/></td>
                    <td class="align-middle">{{ $usuario->name }}</td>
                    <td class="text-center align-middle">{{ count($usuario->actividades_nuevas()) }}</td>
                    <td class="text-center align-middle">{{ count($usuario->actividades_en_curso()) }}</td>
                    <td class="text-center align-middle {{ count($usuario->actividades_enviadas())>0 ? 'bg-danger' : '' }}">{{ count($usuario->actividades_enviadas()) }}</td>
                    <td class="text-center align-middle">{{ count($usuario->actividades_terminadas()) }}</td>
                    <td class="align-middle">
                        <form method="POST"
                              action="{{ route('users.destroy', ['user' => $usuario->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class="btn-group">
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
