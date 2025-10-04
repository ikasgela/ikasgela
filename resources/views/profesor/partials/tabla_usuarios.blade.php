@use(Illuminate\Support\Str)
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th class="p-0"></th>
            <th>
                <input type="checkbox" id="seleccionar_usuarios">
            </th>
            @if(config('ikasgela.avatar_enabled'))
                <th></th>
            @endif
            <th>{{ __('Name') }}</th>
            <th class="text-center">
                @include('profesor.partials.titulo-columna', ['titulo' => trans_choice('tasks.hidden', 2)])
            </th>
            <th class="text-center">
                @include('profesor.partials.titulo-columna', ['titulo' => trans_choice('tasks.new', 2)])
            </th>
            <th class="text-center">
                @include('profesor.partials.titulo-columna', ['titulo' => trans_choice('tasks.accepted', 2)])
            </th>
            <th class="text-center">
                @include('profesor.partials.titulo-columna', ['titulo' => trans_choice('tasks.sent', 2)])
            </th>
            <th class="text-center">
                @include('profesor.partials.titulo-columna', ['titulo' => trans_choice('tasks.reviewed', 2)])
            </th>
            <th class="text-center">
                @include('profesor.partials.titulo-columna', ['titulo' => trans_choice('tasks.archived', 2)])
            </th>
            <th class="text-center">
                @include('profesor.partials.titulo-columna', ['titulo' => trans_choice('tasks.expired', 2)])
            </th>
            <th class="text-center">{{ __('Simultaneous') }}</th>
            <th>{{ __('Activity') }}</th>
            <th>{{ trans_choice('tasks.last', 1) }}</th>
            <th>{{ __('Next') }}</th>
            @if(Auth::user()->hasRole('admin'))
                <th>{{ __('Actions') }}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @php($total_enviadas = 0)
        @php($media = false)
        @foreach($usuarios as $user)
            @if( !$media && session('profesor_filtro_alumnos') == 'P'
                    && $user->num_completadas('base') > $media_grupo )
                <tr class="text-bg-light small fw-light">
                    <td class="p-0"></td>
                    <td colspan="14" class="">{{ __('Mean') }}:
                        {{ $media_grupo_formato }} {{ mb_strtolower(__('Completed activities')) }}</td>
                    @if(Auth::user()->hasRole('admin'))
                        <td></td>
                    @endif
                </tr>
                @php($media = true)
            @endif
            <tr class="table-cell-click" data-href="{{ route('profesor.tareas', [$user->id]) }}">
                <td class="p-0 ps-1
                    @if($user->num_actividades_sin_completar() == 0)
                    bg-success
                    @elseif($user->num_actividades_enviadas_noautoavance() > 0)
                    bg-danger
                    @endif
                    ">
                    &nbsp;
                </td>
                <td>
                    <input form="asignar" type="checkbox"
                           data-chkbox-shiftsel="grupo1"
                           name="usuarios_seleccionados[{{ $user->id }}]" value="{{ $user->id }}">
                </td>
                @if(config('ikasgela.avatar_enabled'))
                    <td class="clickable">@include('users.partials.avatar', ['user' => $user, 'width' => 35])</td>
                @endif
                <td class="clickable">
                    <div class="d-flex align-items-center p-0">
                        <span>{{ $user->full_name }}</span>
                        @include('profesor.partials.status_usuario')
                        @include('profesor.partials.etiquetas_usuario_filtro')
                        @include('profesor.partials.baja_ansiedad_usuario')
                    </div>
                </td>
                <td class="clickable text-center">{{ $user->num_actividades_ocultas() }}</td>
                <td class="clickable text-center">{{ $user->num_actividades_nuevas() }}</td>
                <td class="clickable text-center">{{ $user->num_actividades_aceptadas() }}</td>
                @if(session('profesor_filtro_actividades_examen') == 'E')
                    <td class="clickable text-center {{ $user->num_actividades_enviadas_noautoavance(Auth::user()->curso_actual()) > 0 ? 'bg-danger text-white' : '' }}">
                        {{ $user->num_actividades_enviadas_noautoavance(Auth::user()->curso_actual()) }}
                    </td>
                    @php($total_enviadas += $user->num_actividades_enviadas_noautoavance(Auth::user()->curso_actual()))
                @else
                    <td class="clickable text-center {{ $user->num_actividades_enviadas_noautoavance_noexamen(Auth::user()->curso_actual()) > 0 ? 'bg-danger text-white' : '' }}">
                        {{ $user->num_actividades_enviadas_noautoavance_noexamen(Auth::user()->curso_actual()) }}
                    </td>
                    @php($total_enviadas += $user->num_actividades_enviadas_noautoavance_noexamen(Auth::user()->curso_actual()))
                @endif
                <td class="clickable text-center">{{ $user->num_actividades_revisadas() }}</td>
                <td class="clickable text-center">{{ $user->num_actividades_archivadas() }}</td>
                <td class="clickable text-center {{ $user->num_actividades_caducadas() > 0 ? 'bg-warning text-black' : '' }}">{{ $user->num_actividades_caducadas() }}</td>
                <td class="clickable text-center">{{ $user->max_simultaneas }}</td>
                <td class="clickable text-lowercase">{{ $user->last_active_time }}</td>
                <td class="clickable">
                    <span title="{{ $user->siguiente_actividad()->slug ?? '' }}">
                        {{ Str::limit($user->siguiente_actividad()->slug ?? '', 20) }}
                    </span>
                </td>
                @include('profesor.partials.siguiente_actividad', ['actividad' => $user->siguiente_actividad()])
                @if(Auth::user()->hasRole('admin'))
                    <td>
                        @include('users.partials.acciones')
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
        <tfoot class="thead-dark">
        <tr>
            <th class="p-0"></th>
            <th colspan="6">{{ __('Student total') }}: {{ $usuarios->count() }}</th>
            <th class="text-center">{{ $total_enviadas>0 ? $total_enviadas : '' }}</th>
            <th colspan="100"></th>
        </tr>
        @if(count($etiquetas) > 0)
            <tr>
                <td class="p-0"></td>
                <td colspan="100">
                    {{ __('Filter by tag') }}:
                    @foreach($etiquetas as $etiqueta)
                        <span class="ms-2">
                        {{ html()
                            ->a(route(explode('.', Route::currentRouteName())[0] . '.index.filtro', ['tag_usuario' => $etiqueta]), $etiqueta)
                            ->class('badge bg-body-secondary text-body-secondary') }}
                        </span>
                    @endforeach
                </td>
            </tr>
        @endif
        </tfoot>
    </table>
</div>
