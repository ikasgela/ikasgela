@include('partials.subtitulo', ['subtitulo' => __('Content development')])

<div class="card">
    <div class="card-body">
        @foreach ($unidades as $unidad)
            <h5 class="card-title">
                @isset($unidad->codigo)
                    {{ $unidad->codigo }} -
                @endisset
                @include('unidades.partials.nombre_con_etiquetas')
            </h5>
            <p class="ml-5">{{ $unidad->descripcion }}</p>
            <div class="ml-5 progress" style="height: 24px;">
                @php($porcentaje = $calificaciones->resultados_unidades[$unidad->id]->actividad > 0 ? round($calificaciones->resultados_unidades[$unidad->id]->tarea/$calificaciones->resultados_unidades[$unidad->id]->actividad*100) : 0)
                <div
                    class="progress-bar {{ $porcentaje< ($unidad->hasEtiqueta('examen') ? $calificaciones->minimo_examenes : $calificaciones->minimo_competencias) ? 'bg-warning text-dark' : 'bg-success' }}"
                    role="progressbar"
                    style="width: {{ $porcentaje }}%;"
                    aria-valuenow="{{ $porcentaje }}"
                    aria-valuemin="0"
                    aria-valuemax="100">@if($porcentaje>=15){{ $porcentaje }}&thinsp;%@endif
                </div>
                @if($porcentaje<15)
                    <div class="progress-bar bg-gray-200 text-dark pl-1">{{ $porcentaje }}&thinsp;%</div>
                @endif
            </div>
            <div class="text-muted small text-right">
                {{ $calificaciones->resultados_unidades[$unidad->id]->tarea + 0
                }}/{{ $calificaciones->resultados_unidades[$unidad->id]->actividad + 0 }}
            </div>
            @if(!$loop->last)
                <hr>
            @endif
        @endforeach
    </div>
</div>
