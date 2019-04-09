<div class="row mb-3">
    <div class="col-md">
        <h2>Actividades disponibles</h2>
    </div>
</div>

@include('profesor.partials.selector_unidad')

@if(count($disponibles) > 0)
    <form method="POST" action="{{ route('profesor.asignar_tarea', ['user' => $user->id]) }}">
        @csrf
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Seleccionar</th>
                    <th colspan="2">Actividad</th>
                </tr>
                </thead>
                <tbody>
                @foreach($disponibles as $actividad)
                    <tr>
                        <td>{{ $actividad->id }}</td>
                        <td>
                            <input type="checkbox" name="seleccionadas[]" value="{{ $actividad->id }}">
                        </td>
                        <td>{{ $actividad->nombre }}</td>
                        <td>{{ $actividad->unidad->slug.'/'.$actividad->slug }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @include('layouts.errors')
        <div class="mb-4">
            <button type="submit" class="btn btn-primary">Guardar asignación</button>
            <a href="{{ route('profesor.index') }}" class="btn btn-link text-secondary">Cancelar</a>
        </div>
    </form>
@else
    <div class="row">
        <div class="col-md">
            <p>No hay ninguna actividad disponible.</p>
        </div>
    </div>
@endif
