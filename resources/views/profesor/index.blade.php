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
                <th>{{ __('Name') }}</th>
                <th class="text-center">Ocultas</th>
                <th class="text-center">Nuevas</th>
                <th class="text-center">Aceptadas</th>
                <th class="text-center">Enviadas</th>
                <th class="text-center">Revisadas</th>
                <th class="text-center">Archivadas</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($usuarios as $usuario)
                <tr class="table-row" data-href="{{ route('profesor.tareas', [$usuario->id]) }}">
                    <td><img style="height:35px;" src="{{ $usuario->avatar_url()}}"/></td>
                    <td>{{ $usuario->name }}</td>

                    <td class="text-center">{{ count($usuario->actividades_ocultas()) }}</td>
                    <td class="text-center">{{ count($usuario->actividades_nuevas()) }}</td>
                    <td class="text-center">{{ count($usuario->actividades_aceptadas()) }}</td>
                    <td class="text-center {{ count($usuario->actividades_enviadas())>0 ? 'bg-danger' : '' }}">{{ count($usuario->actividades_enviadas()) }}</td>
                    <td class="text-center">{{ count($usuario->actividades_revisadas()) }}</td>
                    <td class="text-center">{{ count($usuario->actividades_archivadas()) }}</td>
                    <td>
                        <form method="POST"
                              action="{{ route('users.destroy', ['user' => $usuario->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class="btn-group">
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
