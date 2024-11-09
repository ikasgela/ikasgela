<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            @if(!isset($exportar))
                <th></th>
            @endif
            @if(isset($exportar))
                <th>{{ __('Identifier') }}</th>
            @endif
            <th>{{ __('Name') }}</th>
            @if(!isset($exportar))
                <th class="text-center">{{ __('Reload results') }}</th>
            @endif
            <th class="text-center">{{ __('Course passed') }}</th>
            <th class="text-center">{{ __('Continuous evaluation') }}</th>
            <th class="text-center">{{ __('Mandatory activities') }}</th>
            <th class="text-center">{{ __('Calification') }}</th>
            <th class="text-center">{{ __('Published calification') }}</th>
            @if(!isset($exportar))
                <th></th>
            @endif
            @foreach($unidades as $unidad)
                <th class="text-center">{{ $unidad->nombre }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @php($media = false)
        @php($aprobados = 0)
        @php($ajuste_proporcional_nota = $milestone?->ajuste_proporcional_nota ?: $curso?->ajuste_proporcional_nota)
        @foreach($usuarios as $user)
            @php($calificaciones = $user->calcular_calificaciones($mediana, $milestone))
            @if(!$media && !isset($exportar) && session('tutor_filtro_alumnos') == 'P')
                @switch($ajuste_proporcional_nota)
                    @case('mediana')
                        @if($user->num_completadas('base', null, $milestone) >= $mediana)
                            @include('tutor.partials.fila_media')
                            @php($media = true)
                        @endif
                        @break
                    @default
                        @if($user->num_completadas('base', null, $milestone) >= $media_actividades_grupo)
                            @include('tutor.partials.fila_media')
                            @php($media = true)
                        @endif
                @endswitch
            @endif
            <tr>
                @if(isset($exportar))
                    <th>{{ $user->identifier }}</th>
                @endif
                @if(!isset($exportar))
                    @if(session('tutor_informe_anonimo') == 'A' || $user_seleccionado->id == $user->id)
                        <td>@include('users.partials.avatar', ['user' => $user, 'width' => 35])</td>
                    @else
                        <td>@include('users.partials.avatar', ['user' => null, 'width' => 35])</td>
                    @endif
                @endif
                <td>
                    @if(!isset($exportar))
                        @if(session('tutor_informe_anonimo') == 'A' || $user_seleccionado->id == $user->id)
                            {{ html()->form('POST', route('results.alumno'))->open() }}
                            {{ html()->submit($user->full_name)
                                    ->class(['btn btn-link m-0 p-0 text-start', session('tutor_filtro_alumnos') == 'A' ? 'btn-secondary' : 'btn-outline-secondary']) }}
                            {{ html()->hidden('user_id', $user->id) }}
                            {{ html()->form()->close() }}
                        @else
                            -
                        @endif
                    @else
                        {{ $user->full_name }}
                    @endif
                </td>
                @if(!isset($exportar))
                    <td class="text-center">
                        {{ html()->form('POST', route('users.limpiar_cache', [$user->id]))->open() }}
                        {{ html()->submit('<i class="fas fa-broom"></i>')
                                ->attribute('title', __('Reload results'))
                                ->class(['btn btn-sm btn-light', session('tutor_filtro_alumnos') == 'A' ? 'btn-secondary' : 'btn-outline-secondary']) }}
                        {{ html()->form()->close() }}
                    </td>
                @endif
                @php($aprobados += $calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada ? 1 : 0)
                <td class="text-center {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? 'bg-success text-dark' : 'bg-warning text-dark' }}">
                    {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? __('Yes') : __('No') }}
                </td>
                <td class="text-center {{ $calificaciones->hay_nota_manual ? '' : ($calificaciones->evaluacion_continua_superada ? 'bg-success text-dark' : 'bg-warning text-dark') }}">
                    {{ $calificaciones->evaluacion_continua_superada ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}
                </td>
                <td class="text-center
                    @switch($ajuste_proporcional_nota)
                        @case('mediana')
                             {{ $calificaciones->hay_nota_manual ? '' : ($user->num_completadas('base', null, $milestone) < $mediana ? 'bg-warning text-dark' : '') }}
                            @break
                        @default
                            {{ $calificaciones->hay_nota_manual ? '' : ($user->num_completadas('base', null, $milestone) < $media_actividades_grupo ? 'bg-warning text-dark' : '') }}
                    @endswitch
                    ">
                    {{ $user->num_completadas('base', null, $milestone) }}
                </td>
                @php($rango = ($curso?->normalizar_nota || $milestone?->normalizar_nota) ? ['min' => $nota_minima, 'max' => $nota_maxima] : null)
                <td {!! $calificaciones->nota_numerica_normalizada($rango) < 5 ? 'style="color:#e3342f"' : '' !!}
                    class="text-center {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? 'bg-success text-dark' : ($curso?->disponible() ? '' : 'bg-warning text-dark') }}">
                    {{ $calificaciones->nota_final($rango) }}
                </td>
                <td {!! $calificaciones->nota_numerica_normalizada($rango) < 5 ? 'style="color:#e3342f"' : '' !!}
                    class="text-center {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? 'bg-success text-dark' : ($curso?->disponible() ? '' : 'bg-warning text-dark') }}">
                    {{ $calificaciones->nota_publicar($milestone, $rango) }}
                </td>
                @if(!isset($exportar))
                    <td>
                        @if(Auth::user()->hasAnyRole(['profesor','admin']))
                            <a title="{{ __('Manual calification') }}"
                               href="{{ route('profesor.nota_manual.edit', [$user->id, $curso?->id]) }}"
                               class="btn btn-sm {{ $calificaciones->hay_nota_manual ? 'btn-primary' : 'btn-light' }}">
                                <i class="fas fa-pen"></i>
                            </a>
                        @elseif($calificaciones->hay_nota_manual)
                            <i title="{{ __('Manual calification') }}" class="fas fa-pen btn-sm bg-light }}"></i>
                        @endif
                    </td>
                @endif
                @foreach($unidades as $unidad)
                    @php($resultados_unidades = $calificaciones->resultados_unidades)
                    @php($porcentaje = isset($resultados_unidades[$unidad->id]) && $resultados_unidades[$unidad->id]->actividad > 0
                        ? ($resultados_unidades[$unidad->id]?->tarea/$resultados_unidades[$unidad->id]?->actividad*100) : 0)
                    @if(isset($resultados_unidades[$unidad->id]) && $resultados_unidades[$unidad->id]?->actividad > 0)
                        @if(!isset($exportar))
                            <td class="text-center {{ $calificaciones->hay_nota_manual ? '' : ($porcentaje<50 ? 'bg-warning text-dark' : ($unidad->hasEtiquetas(['examen','final']) ? 'bg-success text-dark' : '')) }}">
                                {{ number_format ( $porcentaje, 0 ) }}&thinsp;%
                            </td>
                        @else
                            <td style="{{ $porcentaje<50 ? 'color:#e3342f' : '' }}">
                                {{ number_format ( $porcentaje, 0 ) }}
                            </td>
                        @endif
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
                <th colspan="2">
                    {{ __('Passed') }}: {{ $aprobados }}
                    ({{ formato_decimales($usuarios->count() > 0 ? $aprobados/$usuarios->count()*100 : 0, 2) }}&thinsp;%)
                </th>
                <th class="text-center">{{ __('Total') }}: {{ $num_actividades_obligatorias }}</th>
                <th colspan="{{ $unidades->count() + 3 }}"></th>
            </tr>
            </tfoot>
        @endif
    </table>
</div>
