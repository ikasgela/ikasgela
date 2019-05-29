@include('partials.subtitulo', ['subtitulo' => __('Assigned activities')])

@if(count($actividades) > 0 )
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th class="p-0"></th>
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
                    <td style="width:5px;" class="p-0 {{ $actividad->tarea->estado == 30 ? 'bg-danger' : '' }}"></td>
                    <td>{{ $actividad->tarea->id }}</td>
                    <td>{{ $actividad->unidad->slug.'/'.$actividad->slug }}</td>
                    <td class="text-center">{!! $actividad->tarea->estado == 11 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times text-secondary"></i>' !!}</td>
                    <td class="text-center">{!! $actividad->tarea->estado >= 20 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-center {!! $actividad->tarea->estado == 30 ? 'bg-danger' : '' !!}">
                        {!! $actividad->tarea->estado >= 30 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times text-danger"></i>' !!}
                    </td>
                    <td class="text-center">
                        @switch($actividad->tarea->estado)
                            @case(40)
                            {!! '<i class="fas fa-check text-success"></i>' !!}
                            @break
                            @case(41)
                            {!! '<i class="fas fa-check text-warning"></i>' !!}
                            @break
                            @case(50)
                            @case(60)
                            {!! '<i class="fas fa-check"></i>' !!}
                            @break;
                            @default
                            @if(!$actividad->auto_avance)
                                {!! '<i class="fas fa-times text-danger"></i>' !!}
                            @else
                                {!! '<i class="fas fa-times text-secondary"></i>' !!}
                            @endif
                        @endswitch
                    </td>
                    <td class="text-center">{!! $actividad->tarea->estado >= 50 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    @include('partials.botones_recursos')
                    <td>
                        <form method="POST"
                              action="{{ route('tareas.destroy', ['user' => $user->id, 'actividad'=>$actividad->tarea->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a title="{{ __('Review') }}"
                                   href="{{ route('profesor.revisar', ['user' => $user->id, 'actividad'=>$actividad->tarea->id]) }}"
                                   class="btn btn-light btn-sm"><i class="fas fa-bullhorn"></i></a>
                                <a title="{{ __('Edit task') }}"
                                   href="{{ route('tareas.edit', [$actividad->tarea->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                <a title="{{ __('Edit activity') }}"
                                   href="{{ route('actividades.edit', [$actividad->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-link"></i></a>
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
