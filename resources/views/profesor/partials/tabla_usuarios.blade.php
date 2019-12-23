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
            <th class="text-center">{{ trans_choice('tasks.hidden', 2) }}</th>
            <th class="text-center">{{ trans_choice('tasks.new', 2) }}</th>
            <th class="text-center">{{ trans_choice('tasks.accepted', 2) }}</th>
            <th class="text-center">{{ trans_choice('tasks.sent', 2) }}</th>
            <th class="text-center">{{ trans_choice('tasks.reviewed', 2) }}</th>
            <th class="text-center">{{ trans_choice('tasks.archived', 2) }}</th>
            <th class="text-center">{{ __('Simultaneous') }}</th>
            <th>{{ __('Activity') }}</th>
            <th>{{ trans_choice('tasks.last', 1) }}</th>
            <th>{{ __('Next') }}</th>
            @if(Auth::user()->hasRole('admin'))
                <th class="text-center">{{ __('Actions') }}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @php($total_enviadas = 0)
        @php($media = false)
        @foreach($usuarios as $user)
            @if( !$media && session('profesor_filtro_alumnos') == 'P'
                    && $user->actividades_completadas()->count() > $total_actividades_grupo / $usuarios->count() )
                <tr class="bg-secondary">
                    <th class="p-0"></th>
                    <th colspan="13">{{ __('Mean') }}:
                        {{ number_format ( $total_actividades_grupo / $usuarios->count(), 2) }} {{ __('completed activities') }}</th>
                    @if(Auth::user()->hasRole('admin'))
                        <th></th>
                    @endif
                </tr>
                @php($media = true)
            @endif
            <tr class="table-cell-click" data-href="{{ route('profesor.tareas', [$user->id]) }}">
                <td class="p-0 pl-1
                    @if($user->actividades_sin_completar()->count() == 0)
                    bg-success
                    @elseif($user->actividades_enviadas_noautoavance()->count() > 0)
                    bg-danger
                    @endif
                    ">
                    &nbsp;
                </td>
                <td>
                    <input form="asignar" type="checkbox"
                           name="usuarios_seleccionados[{{ $user->id }}]" value="{{ $user->id }}">
                </td>
                <td class="clickable"><img style="height:35px;" src="{{ $user->avatar_url(70)}}"
                                           onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';"/>
                </td>
                <td class="clickable">
                    {{ $user->name }}
                    @include('profesor.partials.status_usuario')
                </td>
                <td class="clickable text-center">{{ $user->actividades_ocultas()->count() }}</td>
                <td class="clickable text-center">{{ $user->actividades_nuevas()->count() }}</td>
                <td class="clickable text-center">{{ $user->actividades_aceptadas()->count() }}</td>
                <td class="clickable text-center {{ $user->actividades_enviadas_noautoavance()->count() > 0 ? 'bg-danger' : '' }}">{{ $user->actividades_enviadas_noautoavance()->count() }}</td>
                @php($total_enviadas += $user->actividades_enviadas_noautoavance()->count())
                <td class="clickable text-center">{{ $user->actividades_revisadas()->count() }}</td>
                <td class="clickable text-center">{{ $user->actividades_archivadas()->count() }}</td>
                <td class="clickable">{{ $user->max_simultaneas }}</td>
                <td class="clickable">{{ $user->last_active_time }}</td>
                <td class="clickable">{{ $user->actividades_asignadas()->orderBy('id', 'desc')->first()->slug ?? '' }}</td>
                @include('profesor.partials.siguiente_actividad', ['actividad' => $user->actividades_asignadas()->orderBy('id', 'desc')->first()])
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
            <th colspan="6">{{ __('Student total') }}: {{ $usuarios->count() }}</th>
            <th class="text-center">{{ $total_enviadas>0 ? $total_enviadas : '' }}</th>
            <th colspan="6"></th>
            @if(Auth::user()->hasRole('admin'))
                <th></th>
            @endif
        </tr>
        </tfoot>
    </table>
</div>
