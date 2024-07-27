@include('partials.subtitulo', ['subtitulo' => __('Content development')])

<div class="card mb-3">
    <div class="card-body">
        @foreach ($unidades as $unidad)
            <h5 class="card-title">
                @isset($unidad->codigo)
                    {{ $unidad->codigo }} -
                @endisset
                @include('unidades.partials.nombre_con_etiquetas')
            </h5>
            <p class="ms-5">{{ $unidad->descripcion }}</p>
            <div class="ms-5 progress">
                @php($hay_calificacion = $calificaciones->resultados_unidades[$unidad->id]->actividad > 0)
                @php($porcentaje = $hay_calificacion ? round($calificaciones->resultados_unidades[$unidad->id]->tarea/$calificaciones->resultados_unidades[$unidad->id]->actividad*100) : 0)
                <div
                    class="progress-bar {{ $porcentaje< ($unidad->hasEtiqueta('examen') ? ($unidad->hasEtiqueta('final') ? $calificaciones->minimo_examenes_finales : $calificaciones->minimo_examenes) : $calificaciones->minimo_competencias) ? 'bg-warning text-dark' : 'bg-success' }}"
                    role="progressbar"
                    style="width: {{ $porcentaje }}%;"
                    aria-valuenow="{{ $porcentaje }}"
                    aria-valuemin="0"
                    aria-valuemax="100">@if($porcentaje>=20){{ formato_decimales($porcentaje) }}&thinsp;%@endif
                </div>
                @if($hay_calificacion && $porcentaje<20)
                    <div
                        class="progress-bar {{ $porcentaje > 0 ? 'bg-body-secondary bg-opacity-10' : 'bg-warning text-dark w-100' }} text-start ps-2">
                        {{ formato_decimales($porcentaje) }}&thinsp;%
                    </div>
                @endif
            </div>
            <div class="text-secondary small text-end">
                @if($hay_calificacion)
                    {{ $calificaciones->resultados_unidades[$unidad->id]->tarea + 0
                    }}/{{ $calificaciones->resultados_unidades[$unidad->id]->actividad + 0 }}
                @else
                    {{ __('No results yet') }}
                @endif
            </div>
            @if(!$loop->last)
                <hr>
            @endif
        @endforeach
    </div>
</div>
