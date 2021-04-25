<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            @if(!isset($exportar))
                <th></th>
            @endif
            <th>{{ __('Name') }}</th>
            <th class="text-center">{{ __('Course passed') }}</th>
            <th class="text-center">{{ __('Continuous evaluation') }}</th>
            <th class="text-center">{{ __('Mandatory activities') }}</th>
            <th class="text-center">{{ __('Calification') }}</th>
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
        @foreach($usuarios as $user)
            @php($calificaciones = $user->calcular_calificaciones())
            @if(!$media && !isset($exportar) && session('tutor_filtro_alumnos') == 'P'
                    && $user->num_completadas('base') > $media_actividades_grupo)
                @include('tutor.partials.fila_media')
                @php($media = true)
            @endif
            <tr>
                @if(!isset($exportar))
                    <td>@include('users.partials.avatar', ['user' => $user, 'width' => 35])</td>
                @endif
                <td>
                    @if(!isset($exportar))
                        {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST', 'style' => 'display:inline']) !!}
                        {!! Form::button($user->name.' '.$user->surname, ['type' => 'submit', 'class' => 'btn btn-link m-0 p-0 text-dark text-left']) !!}
                        {!! Form::hidden('user_id',$user->id) !!}
                        {!! Form::close() !!}

                        @include('profesor.partials.status_usuario')
                    @else
                        {{ $user->name }} {{ $user->surname }}
                    @endif
                </td>
                @php($aprobados += $calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada ? 1 : 0)
                <td class="text-center {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? 'bg-success text-dark' : 'bg-warning text-dark' }}">
                    {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? __('Yes') : __('No') }}
                </td>
                <td class="text-center {{ $calificaciones->hay_nota_manual ? '' : ($calificaciones->evaluacion_continua_superada ? 'bg-success text-dark' : 'bg-warning text-dark') }}">
                    {{ $calificaciones->evaluacion_continua_superada ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}
                </td>
                <td class="text-center {{ $calificaciones->hay_nota_manual ? '' : ($user->num_completadas('base') < $media_actividades_grupo ? 'bg-warning text-dark' : '') }}">
                    {{ $user->num_completadas('base') }}
                </td>
                <td class="text-center {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado || $calificaciones->nota_manual_superada) ? 'bg-success text-dark' : ($curso->disponible() ? '' : 'bg-warning text-dark') }}">
                    {{ $calificaciones->nota_final }}
                </td>
                @if(!isset($exportar))
                    <td>
                        @if(Auth::user()->hasAnyRole(['profesor','admin']))
                            <a title="{{ __('Manual calification') }}"
                               href="{{ route('profesor.nota_manual.edit', [$user->id, $curso->id]) }}"
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
                    @php($porcentaje = $resultados_unidades[$unidad->id]->actividad > 0
                    ? ($resultados_unidades[$unidad->id]->tarea/$resultados_unidades[$unidad->id]->actividad*100) : 0)
                    @if($resultados_unidades[$unidad->id]->actividad > 0)
                        <td class="text-center {{ $calificaciones->hay_nota_manual ? '' : ($porcentaje<50 ? 'bg-warning text-dark' : ($unidad->hasEtiquetas(['examen','final']) ? 'bg-success text-dark' : '')) }}">
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
                <th colspan="2">{{ __('Student total') }}: {{ $usuarios->count() }}</th>
                <th colspan="2">
                    {{ __('Passed') }}: {{ $aprobados }}
                    ({{ formato_decimales($aprobados/$usuarios->count()*100, 2) }}&thinsp;%)
                </th>
                <th class="text-center">{{ __('Total') }}: {{ $num_actividades_obligatorias }}</th>
                <th colspan="{{ $unidades->count() + 2 }}"></th>
            </tr>
            </tfoot>
        @endif
    </table>
</div>
