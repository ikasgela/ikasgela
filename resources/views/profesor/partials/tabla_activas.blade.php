@use(Illuminate\Support\Str)
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th class="seleccionar_filas">
                <input class="form-check-input" type="checkbox" id="seleccionar_activas">
            </th>
            @if(config('ikasgela.avatar_enabled'))
                <th></th>
            @endif
            <th>{{ __('Student') }}</th>
            <th>{{ __('Activity') }}</th>
            <th></th>
            <th>{{ __('Next') }}</th>
            @if(Auth::user()->hasRole('admin'))
                <th>{{ __('Resources') }}</th>
            @endif
            <th colspan="100">{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $user)
            @foreach($user->actividades as $actividad)
                <tr class="table-cell-click"
                    data-href="{{ route('profesor.revisar', ['user' => $user->id, 'tarea' => $actividad->tarea->id]) }}">
                    <td class="seleccionar_filas">
                        <input class="form-check-input"
                               id="input_actividad_{{ $actividad->tarea->id }}" form="multiple_activas"
                               data-chkbox-shiftsel="grupo_activas"
                               onclick="document.getElementById('input2_actividad_{{ $actividad->tarea->id }}').checked = this.checked"
                               type="checkbox" name="asignadas[]" value="{{ $actividad->tarea->id }}">
                        <input id="input2_actividad_{{ $actividad->tarea->id }}" form="multiple2_activas" class="d-none"
                               type="checkbox" name="asignadas[]" value="{{ $actividad->tarea->id }}">
                    </td>
                    @if($loop->first)
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
                    @else
                        @if(config('ikasgela.avatar_enabled'))
                            <td></td>
                        @endif
                        <td></td>
                    @endif
                    <td class="clickable">
                        @include('actividades.partials.nombre_con_etiquetas', ['ruta' => explode('.', Route::currentRouteName())[0] . '.tareas.filtro', 'slug' => true])
                    </td>
                    @include('profesor.partials.tarea_caducada')
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
                                   class="btn btn-light btn-sm"><i class="bi bi-megaphone"></i></a>
                                @if(Auth::user()->hasRole('admin'))
                                    <a title="{{ __('Edit activity') }}"
                                       href="{{ route('actividades.edit', [$actividad->id]) }}"
                                       class='btn btn-light btn-sm'><i class="bi bi-pencil-square"></i></a>
                                    <a title="{{ __('Edit task') }}"
                                       href="{{ route('tareas.edit', [$actividad->tarea->id]) }}"
                                       class='btn btn-light btn-sm'><i class="bi bi-link-45deg"></i></a>
                                @endif
                                @include('partials.boton_borrar')
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
        <tfoot class="thead-dark">
        <tr>
            <th class="p-0"></th>
            <th colspan="6">{{ __('Student total') }}: {{ $usuarios->count() }}</th>
            <th colspan="100"></th>
        </tr>
        </tfoot>
    </table>
</div>
<div class="d-flex justify-content-end mb-3">
    <div class="me-3">
        {{ html()->form('DELETE', route('tareas.borrar_multiple_activas'))->id('multiple_activas')->open() }}
        <span class="me-2">{{ __('With the selected') }}: </span>
        @include('partials.boton_borrar')
        {{ html()->form()->close() }}
    </div>
    <div>
        {{ html()->form('POST', route('tareas.fecha_finalizacion_multiple_activas'))->id('multiple2_activas')->open() }}
        <div class="input-group input-group-sm">
            {{ html()->input("datetime-local")
                 ->id('fecha_override_activas')->name('fecha_override')
                 ->class(['form-control']) }}
            {{ html()->submit('<i class="bi bi-stopwatch"></i>')
                ->class(['btn btn-light btn-sm'])
                ->attribute('title', __('Override completion date')) }}
        </div>
        {{ html()->form()->close() }}
    </div>
</div>
