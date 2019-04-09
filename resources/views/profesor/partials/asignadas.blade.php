<div class="row mb-3">
    <div class="col-md">
        <h2>Actividades asignadas</h2>
    </div>
</div>

@if(count($actividades) > 0 )
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Actividad</th>
                <th class="text-center">Oculta</th>
                <th class="text-center">Aceptada</th>
                <th class="text-center">Enviada</th>
                <th class="text-center">Revisada</th>
                <th class="text-center">Terminada</th>
                <th>Recursos</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                <tr>
                    <td>{{ $actividad->tarea->id }}</td>
                    <td>{{ $actividad->unidad->slug.'/'.$actividad->slug }}</td>
                    <td class="text-center">{!! $actividad->tarea->estado == 11 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-center">{!! !is_null($actividad->tarea->aceptada) ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-center">{!! !is_null($actividad->tarea->enviada) ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-center">{!! !is_null($actividad->tarea->revisada) ? $actividad->tarea->estado == 41 ? '<i class="fas fa-check text-warning"></i>' : '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-center">{!! !is_null($actividad->tarea->terminada) || !is_null($actividad->tarea->archivada) ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    @include('partials.botones_recursos')
                    <td>
                        <form method="POST"
                              action="{{ route('tareas.destroy', ['user' => $user->id, 'actividad'=>$actividad->tarea->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a href="{{ route('profesor.revisar', ['user' => $user->id, 'actividad'=>$actividad->tarea->id]) }}"
                                   class="btn btn-light btn-sm"><i class="fas fa-bullhorn"></i></a>
                                <a href="#"
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
@else
    <div class="row">
        <div class="col-md">
            <p>El usuario no tiene actividades asignadas.</p>
        </div>
    </div>
@endif
