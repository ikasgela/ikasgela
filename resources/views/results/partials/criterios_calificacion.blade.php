<div class="card">
    <div class="card-body">
        <h5 class="card-title">Criterios de calificación aplicados</h5>
        <ul class="card-text mb-1">
            @if($curso->minimo_entregadas > 0)
                <li>Se deben haber realizado al menos el {{ $curso->minimo_entregadas }}% de las actividades propuestas
                    de cada unidad.

                    @if($actividades_obligatorias_superadas)
                        <i class="fas fa-check text-success"></i>
                    @else
                        <i class="fas fa-times text-danger"></i>
                    @endif
                </li>
            @endif
            @if($curso->minimo_competencias > 0)
                <li>Se debe superar el {{ $curso->minimo_competencias }}% en cada una de las competencias de forma
                    individual.

                    @if($competencias_50_porciento)
                        <i class="fas fa-check text-success"></i>
                    @else
                        <i class="fas fa-times text-danger"></i>
                    @endif
                </li>
            @endif
            @if($curso->examenes_obligatorios)
                <li>Se deben haber superado el {{ $curso->minimo_examenes }}% en las pruebas teórico-prácticas
                    obligatorias de cada competencia.

                    @if($pruebas_evaluacion)
                        <i class="fas fa-check text-success"></i>
                    @else
                        <i class="fas fa-times text-danger"></i>
                    @endif
                </li>
            @endif
        </ul>
    </div>
</div>
