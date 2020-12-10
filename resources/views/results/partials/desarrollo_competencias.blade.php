@include('partials.subtitulo', ['subtitulo' => __('Skills development')])

@if(count($skills_curso) > 0)
    {{-- Tarjeta --}}
    <div class="card">
        <div class="card-body">
            @foreach ($skills_curso as $skill)
                <h5 class="card-title">{{ $skill->name }}</h5>
                <p class="ml-5">{{ $skill->description }}</p>

                @php($peso_examenes = $skill->peso_examen)
                @php($peso_actividades = 100-$skill->peso_examen)

                @php($resultado = $resultados[$skill->id])

                <div class="row no-gutters ml-5">
                    <div class="col" style="flex: 0 0 {{ $peso_actividades }}%;">Actividades</div>
                    @if($peso_examenes>0)
                        <div style="flex: 0 0 {{ $peso_examenes }}%;">Exámenes</div>
                    @endif
                </div>

                <div class="progress ml-5" style="height: 24px;">
                    @php($porcentaje_tarea = $resultado->porcentaje_tarea())
                    <div class="progress-bar" role="progressbar"
                         style="width: {{ $peso_actividades * $porcentaje_tarea / 100 }}%"
                         aria-valuenow="{{ $porcentaje_tarea }}"
                         aria-valuemin="0" aria-valuemax="100"
                         title="{{ $resultado->puntos_tarea }}/{{ $resultado->puntos_totales_tarea }}">
                        {{ formato_decimales($porcentaje_tarea) }}&thinsp;%
                    </div>
                    <div class="progress-bar bg-gray-200"
                         style="width: {{ $peso_actividades * (100-$porcentaje_tarea) / 100 }}%"></div>

                    @if($peso_examenes>0)
                        @php($porcentaje_examen = $resultado->porcentaje_examen())
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $peso_examenes * $porcentaje_examen / 100 }}%"
                             aria-valuenow="{{ $porcentaje_examen }}" aria-valuemin="0" aria-valuemax="100"
                             title="{{ $resultado->puntos_examen }}/{{ $resultado->puntos_totales_examen }}">
                            {{ formato_decimales($porcentaje_examen) }}&thinsp;%
                        </div>
                        <div class="progress-bar bg-gray-200"
                             style="width: {{ $peso_examenes * (100-$porcentaje_examen) / 100 }}%"></div>
                    @endif
                </div>

                <div class="row no-gutters ml-5">
                    <div class="col text-muted small" style="flex: 0 0 10%;">0&thinsp;%</div>
                    @if($peso_examenes>0)
                        <div class="col text-muted small text-right pr-1 border-right"
                             style="flex: 0 0 {{ $peso_actividades-10 }}%;">{{ $peso_actividades }}&thinsp;%
                        </div>
                        <div class="col text-muted small text-right"
                             style="flex: 0 0 {{ $peso_examenes }}%;">100&thinsp;%
                        </div>
                    @else
                        <div class="col text-muted small text-right"
                             style="flex: 0 0 90%;">100&thinsp;%
                        </div>
                    @endif
                </div>

                <div class="row no-gutters ml-5 mt-2">
                    <div class="col" style="flex: 0 0 {{ $peso_actividades }}%;">
                        <span>Total de la competencia</span>
                    </div>
                </div>

                <div class="ml-5 progress" style="height: 24px;">
                    @php($porcentaje_competencia = $resultado->porcentaje_competencia())
                    <div
                        class="progress-bar {{ $porcentaje_competencia < $minimo_competencias ? 'bg-warning text-dark' : 'bg-success' }}"
                        role="progressbar"
                        style="width: {{ $porcentaje_competencia }}%;"
                        aria-valuenow="{{ $porcentaje_competencia }}"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        title="{{ $resultado->tarea }}/{{ $resultado->actividad }}">
                        {{ formato_decimales($porcentaje_competencia) }}&thinsp;%
                    </div>
                </div>

                @if(!$loop->last)
                    <div class="mb-3">&nbsp;</div>
                    <hr>
                @else
                    <div>&nbsp;</div>
                @endif
            @endforeach
        </div>
    </div>
    {{-- Fin tarjeta--}}
@else
    <p>{{ __('No skills assigned.') }}</p>
@endif
