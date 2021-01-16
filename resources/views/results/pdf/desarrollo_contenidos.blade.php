@include('partials.subtitulo', ['subtitulo' => __('Content development')])

<table class="tabla-datos">
    <tr>
        <th class="text-left">{{ __('Unit') }}</th>
        <th>{{ __('Score') }}</th>
        <th>{{ __('Total') }}</th>
    </tr>
    @foreach($unidades as $unidad)

        @php($porcentaje = $calificaciones->resultados_unidades[$unidad->id]->actividad > 0 ? round($calificaciones->resultados_unidades[$unidad->id]->tarea/$calificaciones->resultados_unidades[$unidad->id]->actividad*100) : 0)

        <tr>
            <td>
                @isset($unidad->codigo)
                    {{ $unidad->codigo }} -
                @endisset
                @include('unidades.partials.nombre_con_etiquetas', ['pdf' => true])
            </td>
            <td class="text-center">
                {{ $calificaciones->resultados_unidades[$unidad->id]->tarea + 0
             }}/{{ $calificaciones->resultados_unidades[$unidad->id]->actividad + 0 }}
            </td>
            <td class="text-center {{ $porcentaje< ($unidad->hasEtiqueta('examen') ? $calificaciones->minimo_examenes : $calificaciones->minimo_competencias) ? 'bg-warning text-dark' : 'bg-success' }}">
                {{ formato_decimales($porcentaje) }}&thinsp;%
            </td>
        </tr>
    @endforeach
</table>
