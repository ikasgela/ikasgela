@include('partials.subtitulo', ['subtitulo' => __('Assigned activities')])

@if($actividades->count() > 0 )
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th class="p-0"></th>
                <th>
                    <input type="checkbox" id="seleccionar_asignadas">
                </th>
                <th>#</th>
                <th>{{ __('Activity') }}</th>
                <th class="text-center">{{ trans_choice('tasks.hidden', 1) }}</th>
                <th class="text-center">{{ trans_choice('tasks.accepted', 1) }}</th>
                <th class="text-center">{{ trans_choice('tasks.sent', 1) }}</th>
                <th class="text-center">{{ trans_choice('tasks.reviewed', 1) }}</th>
                <th class="text-center">{{ __('Score') }}</th>
                <th class="text-center">{{ trans_choice('tasks.finished', 1) }}</th>
                <th>{{ __('Next') }}</th>
                @if(Auth::user()->hasRole('admin'))
                    <th>{{ __('Resources') }}</th>
                @endif
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                <tr class="table-cell-click">
                    <td class="p-0 pl-1 {{ $actividad->tarea->estado == 30 && !$actividad->auto_avance ? 'bg-danger' : '' }}">
                        &nbsp;
                    </td>
                    <td>
                        <input form="multiple" type="checkbox" name="asignadas[]" value="{{ $actividad->tarea->id }}">
                    </td>
                    <td>{{ $actividad->tarea->id }}</td>
                    <td>
                        <span class="mr-2">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</span>
                        @foreach($actividad->etiquetas() as $etiqueta)
                            {!! '<span class="badge badge-secondary">'.$etiqueta.'</span>' !!}
                        @endforeach
                    </td>
                    <td class="text-center">{!! $actividad->tarea->estado == 11 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times text-secondary"></i>' !!}</td>
                    <td class="text-center">{!! $actividad->tarea->estado >= 20 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-center {!! $actividad->tarea->estado == 30 && !$actividad->auto_avance ? 'bg-danger text-white' : '' !!}">
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
                    <td class="text-center">{{ $actividad->tarea->puntuacion }}</td>
                    <td class="text-center">{!! $actividad->tarea->estado >= 50 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    @include('profesor.partials.siguiente_actividad')
                    @if(Auth::user()->hasRole('admin'))
                        @include('partials.botones_recursos')
                    @endif
                    <td>
                        <form method="POST"
                              action="{{ route('tareas.destroy', ['user' => $user->id, 'tarea' => $actividad->tarea->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a title="{{ __('Review') }}"
                                   href="{{ route('profesor.revisar', ['user' => $user->id, 'tarea' => $actividad->tarea->id]) }}"
                                   class="btn btn-light btn-sm"><i class="fas fa-bullhorn"></i></a>
                                @if(Auth::user()->hasRole('admin'))
                                    <a title="{{ __('Edit activity') }}"
                                       href="{{ route('actividades.edit', [$actividad->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                    <a title="{{ __('Edit task') }}"
                                       href="{{ route('tareas.edit', [$actividad->tarea->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-link"></i></a>
                                @endif
                                @include('partials.boton_borrar')
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="thead-dark">
            <tr>
                <th colspan="6"></th>
                <th class="text-center">{{ $user->actividades_enviadas_noautoavance()->count() > 0 ? $user->actividades_enviadas_noautoavance()->count() : '0' }}</th>
                <th colspan="6"></th>
            </tr>
            <tr>
                <td colspan="6">
                    <div class="form-inline">
                        {!! Form::open(['route' => ['tareas.borrar_multiple', $user->id], 'method' => 'POST', 'id' => 'multiple']) !!}
                        <span>{{ __('With the selected') }}: </span>
                        @include('partials.boton_borrar')
                        {!! Form::close() !!}
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $actividades->appends(['disponibles' => $disponibles->currentPage()])->links() }}
    </div>
@else
    <div class="row">
        <div class="col-md">
            <p>{{ __("The user doesn't have any assigned activities.") }}</p>
        </div>
    </div>
@endif
