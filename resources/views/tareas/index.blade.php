@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-6">
            <h1>Asignar actividades</h1>

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ $user->name }}</h5>
                            <a href="mailto:{{ $user->email }}" class="card-link">{{ $user->email }}</a>
                        </div>
                        <div class="col-md-4">
                            <img style="width:100px;" src="{{ $user->avatar_url()}}">
                        </div>
                    </div>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md">
            <h2>Actividades asignadas</h2>
        </div>
    </div>

    <?php if (count($actividades) > 0 ) { ?>
    <div class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>Tarea</th>
                <th>Actividad</th>
                <th>Aceptada</th>
                <th>Feedback</th>
                <th>Recursos</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                <tr>
                    <td class="py-3">{{ $actividad->tarea->id }}</td>
                    <td class="py-3">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</td>
                    <td class="py-3">{{ $actividad->tarea->aceptada != null ? \Carbon\Carbon::parse($actividad->tarea->aceptada)->timezone('Europe/Madrid')->locale('es_ES')->isoFormat('LLLL') : 'No' }}</td>
                    <td class="py-3">{{ $actividad->tarea->feedback }}</td>
                    <td>
                        <a href="{{ route('youtube_videos.actividad', [$actividad->id]) }}"
                           class='btn btn-outline-dark'>Youtube</a>
                        <a href="{{ route('intellij_projects.actividad', [$actividad->id]) }}"
                           class='btn btn-outline-dark'>IntelliJ</a>
                    </td>
                    <td>
                        <form method="POST"
                              action="{{ route('tareas.destroy', ['user' => $user->id, 'actividad'=>$actividad->tarea->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
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
    <?php } else { ?>
    <div class="row">
        <div class="col-md">
            <p>El usuario no tiene actividades asignadas.</p>
        </div>
    </div>
    <?php } ?>

    <div class="row mb-3">
        <div class="col-md">
            <h2>Actividades disponibles</h2>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            {!! Form::open(['route' => ['tareas.index', $user->id]]) !!}

            <div class="form-group row">
                {!! Form::label('unidad', __('Unit'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-8">
                    <select class="form-control" id="unidad_id" name="unidad_id">
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id }}" {{ session('profesor_unidad_actual') == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 mt-3 mt-sm-0">
                    <button type="submit" class="btn btn-primary">{{ __('Change') }}</button>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

    <?php if (count($disponibles) > 0 ) { ?>
    <form method="POST" action="{{ route('tareas.asignar', ['user' => $user->id]) }}">
        @csrf

        <div class="table-responsive">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th>Seleccionar</th>
                    <th>Actividad</th>
                </tr>
                </thead>
                <tbody>
                @foreach($disponibles as $actividad)
                    <tr>
                        <td class="py-3"><input type="checkbox" name="seleccionadas[]" value="{{ $actividad->id }}">
                        </td>
                        <td class="py-3">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @include('layouts.errors')

        <div>
            <button type="submit" class="btn btn-primary">Guardar asignación</button>
            <a href="{{ route('users.index') }}" class="btn btn-link text-secondary">Cancelar</a>
        </div>

    </form>
    <?php } else { ?>
    <div class="row">
        <div class="col-md">
            <p>No hay ninguna actividad disponible.</p>
        </div>
    </div>
    <?php } ?>

@endsection
