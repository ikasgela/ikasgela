<div class="row mb-3">
    <div class="col-md">
        <h2>Actividades asignadas</h2>
    </div>
</div>

@if(count($actividades) > 0 )
    <div class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Actividad</th>
                <th>Aceptada</th>
                <th>Enviada</th>
                <th>Revisada</th>
                <th>Recursos</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                <tr>
                    <td class="py-3">{{ $actividad->tarea->id }}</td>
                    <td class="py-3">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</td>
                    <td class="py-3">{{ !is_null($actividad->tarea->aceptada) ? __('Yes') : __('No') }}</td>
                    <td class="py-3">{{ !is_null($actividad->tarea->enviada) ? __('Yes') : __('No') }}</td>
                    <td class="py-3">{{ !is_null($actividad->tarea->revisada) ? __('Yes') : __('No') }}</td>
                    <td>
                        <div class='btn-group'>
                            <a href="{{ route('youtube_videos.actividad', [$actividad->id]) }}"
                               class="btn btn-light btn-sm"><i class="fab fa-youtube"></i></a>
                            <a href="{{ route('intellij_projects.actividad', [$actividad->id]) }}"
                               class="btn btn-light btn-sm"><i class="fab fa-java"></i></a>
                        </div>
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
@else
    <div class="row">
        <div class="col-md">
            <p>El usuario no tiene actividades asignadas.</p>
        </div>
    </div>
@endif
