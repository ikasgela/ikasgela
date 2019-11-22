<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th></th>
            <th>{{ __('Name') }}</th>
            <th class="text-center">{{ __('Completed activities') }}</th>
            @foreach($unidades as $unidad)
                <th class="text-center">{{ $unidad->nombre }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $user)
            <tr>
                <td><img style="height:35px;" src="{{ $user->avatar_url(70)}}"
                         onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';"/>
                </td>
                <td>
                    <a href="mailto:{{ $user->email }}" class="text-dark">{{ $user->name }}</a>
                    @include('profesor.partials.status_usuario')
                </td>
                <td class="text-center {{ $user->num_completadas('base') < $media_actividades_grupo ? 'bg-warning text-dark' : '' }}">{{ $user->num_completadas('base') }}</td>
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
            </tr>
        @endforeach
        </tbody>
        <tfoot class="thead-dark">
        <tr class="bg-secondary">
            <td colspan="2"></td>
            <td class="text-center">{{ __('Mean') }}:
                {{ number_format ( $total_actividades_grupo / $usuarios->count(), 2) }}</td>
            <td colspan="{{ $unidades->count() }}"></td>
        </tr>
        <tr>
            <th colspan="{{ $unidades->count() + 4 }}">{{ __('Student total') }}: {{ $usuarios->count() }}</th>
        </tr>
        </tfoot>
    </table>
</div>
