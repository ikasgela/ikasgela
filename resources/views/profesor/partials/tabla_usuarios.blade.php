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
                <td class="clickable">{{ $usuario->name }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_ocultas()) }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_nuevas()) }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_aceptadas()) }}</td>
                <td class="clickable text-center {{ count($usuario->actividades_enviadas())>0 ? 'bg-danger' : '' }}">{{ count($usuario->actividades_enviadas()) }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_revisadas()) }}</td>
                <td class="clickable text-center">{{ count($usuario->actividades_archivadas()) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
