<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th class="p-0"></th>
            <th>
                <input type="checkbox" id="seleccionar_usuarios">
            </th>
            <th></th>
            <th>{{ __('Name') }}</th>
            <th class="text-center">Ocultas</th>
            <th class="text-center">Nuevas</th>
            <th class="text-center">Aceptadas</th>
            <th class="text-center">Enviadas</th>
            <th class="text-center">Revisadas</th>
            <th class="text-center">Archivadas</th>
            <th>{{ __('Activity') }}</th>
            @if(Auth::user()->hasRole('admin'))
                <th class="text-center">Acciones</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $user)
            <tr class="table-cell-click" data-href="{{ route('profesor.tareas', [$user->id]) }}">
                <td style="width:5px;"
                    class="p-0 {{ $user->actividades_enviadas()->count() > 0 ? 'bg-danger' : '' }}
                    {{ $user->actividades_asignadas()->count() == 0 ? 'bg-secondary' : '' }}">
                    &nbsp;
                </td>
                <td>
                    <input form="asignar" type="checkbox"
                           name="usuarios_seleccionados[]" value="{{ $user->id }}">
                </td>
                <td class="clickable"><img style="height:35px;" src="{{ $user->avatar_url(70)}}"/></td>
                <td class="clickable">
                    {{ $user->name }}
                    @include('profesor.partials.status_usuario')
                </td>
                <td class="clickable text-center">{{ $user->actividades_ocultas()->count() }}</td>
                <td class="clickable text-center">{{ $user->actividades_nuevas()->count() }}</td>
                <td class="clickable text-center">{{ $user->actividades_aceptadas()->count() }}</td>
                <td class="clickable text-center {{ $user->actividades_enviadas()->count() > 0 ? 'bg-danger' : '' }}">{{ $user->actividades_enviadas()->count() }}</td>
                <td class="clickable text-center">{{ $user->actividades_revisadas()->count() }}</td>
                <td class="clickable text-center">{{ $user->actividades_archivadas()->count() }}</td>
                <td>{{ $user->last_active_time }}</td>
                @if(Auth::user()->hasRole('admin'))
                    <td class="text-center">
                        <form method="POST" action="{{ route('users.destroy', [$user->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('users.edit', [$user->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                @include('partials.boton_borrar')
                            </div>
                        </form>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
        <tfoot class="thead-dark">
        <tr>
            <th class="p-0"></th>
            <th colspan="10">Total de alumnos: {{ $usuarios->count() }}</th>
            @if(Auth::user()->hasRole('admin'))
                <th></th>
            @endif
        </tr>
        </tfoot>
    </table>
</div>
