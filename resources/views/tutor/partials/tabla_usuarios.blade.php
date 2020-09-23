<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            @if(!isset($exportar))
                <th></th>
            @endif
            <th>{{ __('Name') }}</th>
            <th class="text-center">{{ __('Continuous evaluation') }}</th>
            <th class="text-center">{{ __('Mandatory activities') }}</th>
            <th class="text-center">{{ __('Calification') }}</th>
            @foreach($unidades as $unidad)
                <th class="text-center">{{ $unidad->nombre }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @php($media = false)
        @foreach($usuarios as $user)
            @if(!$media && !isset($exportar) && session('tutor_filtro_alumnos') == 'P'
                    && $user->num_completadas('base') > $media_actividades_grupo)
                @include('tutor.partials.fila_media')
                @php($media = true)
            @endif
            <tr>
                @if(!isset($exportar))
                    <td><img style="height:35px;" src="{{ $user->avatar_url(70)}}"
                             onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';"/>
                    </td>
                @endif
                <td>
                    @if(!isset($exportar))
                        <a href="mailto:{{ $user->email }}" class="text-dark">{{ $user->name }} {{ $user->surname }}</a>
                    @else
                        {{ $user->name }} {{ $user->surname }}
                    @endif
                    @include('profesor.partials.status_usuario')
                </td>
                <td class="text-center {{ ($actividades_obligatorias[$user->id] || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion[$user->id] || $num_pruebas_evaluacion[$user->id] == 0)
                && isset($competencias_50_porciento[$user->id]) && $competencias_50_porciento[$user->id] && $notas[$user->id] >= 5 ? 'bg-success text-white' : 'bg-warning text-dark' }}">{{ ($actividades_obligatorias[$user->id] || $num_actividades_obligatorias == 0)
                && ($pruebas_evaluacion[$user->id] || $num_pruebas_evaluacion[$user->id] == 0)
                && isset($competencias_50_porciento[$user->id]) && $competencias_50_porciento[$user->id] && $notas[$user->id] >= 5 ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</td>
                <td class="text-center {{ $user->num_completadas('base') < $media_actividades_grupo ? 'bg-warning text-dark' : '' }}">{{ $user->num_completadas('base') }}</td>
                <td class="text-center {{ $notas[$user->id] < 5 ? 'bg-warning text-dark' : '' }}">{{ $notas[$user->id] }}</td>
                @foreach($unidades as $unidad)
                    @php($porcentaje = $resultados_usuario_unidades[$user->id][$unidad->id]->actividad > 0
                    ? ($resultados_usuario_unidades[$user->id][$unidad->id]->tarea/$resultados_usuario_unidades[$user->id][$unidad->id]->actividad*100) : 0)
                    @if($resultados_usuario_unidades[$user->id][$unidad->id]->actividad > 0)
                        <td class="text-center {{ $porcentaje<50 ? 'bg-warning text-dark' : '' }}">
                            {{ number_format ( $porcentaje, 0 ) }}{{ !isset($exportar) ? ' %' : '' }}
                        </td>
                    @else
                        <td class="text-center">{{ !isset($exportar) ? '-' : '' }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
        </tbody>
        @if(!isset($exportar))
            <tfoot class="thead-dark">
            @if(!$media)
                @include('tutor.partials.fila_media')
            @endif
            <tr>
                <th colspan="3">{{ __('Student total') }}: {{ $usuarios->count() }}</th>
                <th class="text-center">{{ $num_actividades_obligatorias }}</th>
                <th colspan="{{ $unidades->count() + 1 }}"></th>
            </tr>
            </tfoot>
        @endif
    </table>
</div>
