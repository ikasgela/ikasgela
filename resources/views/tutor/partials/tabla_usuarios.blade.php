<div class="table">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th></th>
            <th>{{ __('Name') }}</th>
            @foreach($unidades as $unidad)
                <th class="text-center">{{ $unidad->nombre }}</th>
            @endforeach
            <th class="text-center">{{ trans_choice('tasks.completed', 2) }}</th>
            <th>{{ __('Activity') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $user)
            <tr>
                <td><img style="height:35px;" src="{{ $user->avatar_url(70)}}"
                         onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';"/>
                </td>
                <td>
                    {{ $user->name }}
                    @include('profesor.partials.status_usuario')
                </td>
                @foreach($unidades as $unidad)
                    @php($porcentaje = $resultados_usuario_unidades[$user->id][$unidad->id]->actividad > 0
                    ? ($resultados_usuario_unidades[$user->id][$unidad->id]->tarea/$resultados_usuario_unidades[$user->id][$unidad->id]->actividad*100) : 0)
                    @if($resultados_usuario_unidades[$user->id][$unidad->id]->actividad > 0)
                        <td class="text-center {{ $porcentaje<50 ? 'bg-warning text-dark' : '' }}">
                            {{ number_format ( $porcentaje, 2 ) }} %
                        </td>
                    @else
                        <td class="text-center">-</td>
                    @endif
                @endforeach
                <td class="text-center {{ $user->actividades_completadas()->count() < $total_actividades_grupo / $usuarios->count() ? 'bg-warning text-dark' : '' }}">{{ $user->actividades_completadas()->count() }}</td>
                <td>{{ $user->last_active_time }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="thead-dark">
        <tr class="bg-secondary">
            <td colspan="{{ $unidades->count() + 2 }}" class="text-right">{{ __('Mean') }}:</td>
            <td class="text-center">{{ number_format ( $total_actividades_grupo / $usuarios->count(), 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <th colspan="{{ $unidades->count() + 4 }}">{{ __('Student total') }}: {{ $usuarios->count() }}</th>
        </tr>
        </tfoot>
    </table>
</div>
