<div class="card">
    <div class="card-body">
        @if(str_starts_with(\Route::current()->getName(),'results.pdf'))
            <h3 class="card-title">Criterios de calificación aplicados</h3>
        @else
            <h5 class="card-title">Criterios de calificación aplicados</h5>
        @endif
        <ul class="card-text mb-1">
            @if(!$calificaciones->examen_final)
                @if($curso->minimo_entregadas > 0)
                    <li>Se deben haber realizado al menos el {{ formato_decimales($curso->minimo_entregadas) }}&thinsp;%
                        de las actividades propuestas de cada unidad.
                        @include('results.partials.criterio_superado', ['criterio' => $calificaciones->actividades_obligatorias_superadas])
                    </li>
                @endif
                @if($curso->minimo_competencias > 0)
                    <li>Se debe superar el {{ formato_decimales($curso->minimo_competencias) }}&thinsp;% en cada una de
                        las competencias de forma individual.
                        @include('results.partials.criterio_superado', ['criterio' => $calificaciones->competencias_50_porciento])
                    </li>
                @endif
                @if($curso->examenes_obligatorios)
                    <li>Se deben haber superado el {{ formato_decimales($curso->minimo_examenes) }}&thinsp;% en las
                        pruebas teórico-prácticas obligatorias de cada competencia.
                        @include('results.partials.criterio_superado', ['criterio' => $calificaciones->pruebas_evaluacion])
                    </li>
                @endif
            @else
                <li>Se debe superar el {{ formato_decimales($curso->minimo_examenes) }}&thinsp;% en las pruebas de
                    evaluación final, recuperando un máximo del
                    {{ formato_decimales($curso->maximo_recuperable_examenes_finales) }}&thinsp;% de la nota.
                    @include('results.partials.criterio_superado', ['criterio' => $calificaciones->examen_final_superado])
                </li>
            @endif
        </ul>
    </div>
</div>
