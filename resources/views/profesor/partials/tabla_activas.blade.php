@use(Illuminate\Support\Str)
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            @if(config('ikasgela.avatar_enabled'))
                <th></th>
            @endif
            <th>{{ __('Student') }}</th>
            <th>{{ __('Activity') }}</th>
            <th>{{ __('Next') }}</th>
            @if(Auth::user()->hasRole('admin'))
                <th>{{ __('Resources') }}</th>
            @endif
            <th colspan="100">{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $user)
            @foreach($user->actividades()->where('auto_avance', false)->tag('examen', false)->whereIn('estado', [10, 11, 20, 21])->get() as $actividad)
                <tr class="table-cell-click"
                    data-href="{{ route('profesor.revisar', ['user' => $user->id, 'tarea' => $actividad->tarea->id]) }}">
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
