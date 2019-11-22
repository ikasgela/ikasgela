<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th></th>
            <th>{{ __('Name') }}</th>
            <th class="text-center">{{ __('Continuous evaluation') }}</th>
            <th class="text-center">{{ __('Completed activities') }}</th>
            <th class="text-center">{{ __('Calification') }}</th>
            @foreach($unidades as $unidad)
                <th class="text-center">{{ $unidad->nombre }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @php($media = false)
        @foreach($usuarios as $user)
            @if(!$media && session('tutor_filtro_alumnos') == 'P'
                    && $user->num_completadas('base') > $media_actividades_grupo)
                @include('tutor.partials.fila_media')
                @php($media = true)
            @endif
            <tr>
                <td><img style="height:35px;" src="{{ $user->avatar_url(70)}}"
                         onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';"/>
                </td>
                <td>
                    <a href="mailto:{{ $user->email }}" class="text-dark">{{ $user->name }}</a>
                    @include('profesor.partials.status_usuario')
                </td>
                <td class="text-center {{ ($actividades_obligatorias[$user->id] || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion[$user->id] || $num_pruebas_evaluacion[$user->id] == 0)
                && $competencias_50_porciento[$user->id] && $notas[$user->id] >= 5 ? 'bg-success text-white' : 'bg-warning text-dark' }}">{{ ($actividades_obligatorias[$user->id] || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion[$user->id] || $num_pruebas_evaluacion[$user->id] == 0)
                && $competencias_50_porciento[$user->id] && $notas[$user->id] >= 5 ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</td>
                <td class="text-center {{ $user->num_completadas('base') < $media_actividades_grupo ? 'bg-warning text-dark' : '' }}">{{ $user->num_completadas('base') }}</td>
                <td class="text-center {{ $notas[$user->id] < 5 ? 'bg-warning text-dark' : '' }}">{{ $notas[$user->id] }}</td>
                @foreach($unidades as $unidad)
                    @php($porcentaje = $resultados_usuario_unidades[$user->id][$unidad->id]->actividad > 0
                    ? ($resultados_usuario_unidades[$user->id][$unidad->id]->tarea/$resultados_usuario_unidades[$user->id][$unidad->id]->actividad*100) : 0)
                    @if($resultados_usuario_unidades[$user->id][$unidad->id]->actividad > 0)
                        <td class="text-center {{ $porcentaje<50 ? 'bg-warning text-dark' : '' }}">
                            {{ number_format ( $porcentaje, 0 ) }} %
                        </td>
                    @else
                        <td class="text-center">-</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
        </tbody>
        <tfoot class="thead-dark">
        @if(!$media)
            @include('tutor.partials.fila_media')
        @endif
        <tr>
            <th colspan="{{ $unidades->count() + 5 }}">{{ __('Student total') }}: {{ $usuarios->count() }}</th>
        </tr>
        </tfoot>
    </table>
</div>
