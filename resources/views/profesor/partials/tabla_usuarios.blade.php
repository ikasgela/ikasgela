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
        @foreach($usuarios as $usuario)
            <tr class="table-cell-click" data-href="{{ route('profesor.tareas', [$usuario->id]) }}">
                <td style="width:5px;"
                    class="p-0 {{ count($usuario->actividades_enviadas())>0 ? 'bg-danger' : '' }}"></td>
                <td>
                    <input form="asignar" type="checkbox"
                           name="usuarios_seleccionados[]" value="{{ $usuario->id }}">
                </td>
                <td class="clickable"><img style="height:35px;" src="{{ $usuario->avatar_url(70)}}"/></td>
                <td class="clickable">
                    {{ $usuario->name }}
                    {!! $usuario->isBlocked() ? '<span class="badge badge-secondary ml-2">'.__('Blocked').'</span>' : '' !!}
                    {!! !$usuario->isVerified() ? '<span class="badge badge-secondary ml-2">'.__('Unverified').'</span>' : '' !!}
                </td>
                <td class="clickable text-center">{{ count($usuario->actividades_ocultas()) }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_nuevas()) }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_aceptadas()) }}</td>
                <td class="clickable text-center {{ count($usuario->actividades_enviadas())>0 ? 'bg-danger' : '' }}">{{ count($usuario->actividades_enviadas()) }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_revisadas()) }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_archivadas()) }}</td>
                <td>{{ $usuario->last_active_time }}</td>
                @if(Auth::user()->hasRole('admin'))
                    <td class="text-center">
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('users.edit', [$usuario->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
        <tfoot class="thead-dark">
        <tr>
            <th class="p-0"></th>
            <th colspan="10">Total de alumnos: {{ count($usuarios) }}</th>
            @if(Auth::user()->hasRole('admin'))
                <th></th>
            @endif
        </tr>
        </tfoot>
    </table>
</div>
