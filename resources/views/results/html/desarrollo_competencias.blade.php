@include('partials.subtitulo', ['subtitulo' => __('Skills development')])

@if(count($calificaciones->skills_curso) > 0)
    {{-- Tarjeta --}}
    <div class="card mb-3">
        <div class="card-body">
            @foreach ($calificaciones->skills_curso as $skill)
                <h5 class="card-title">{{ $skill->name }}</h5>
                <p class="ms-5">{{ $skill->description }}</p>

                @php($peso_examenes = $skill->peso_examen)
                @php($peso_actividades = 100-$skill->peso_examen)

                @php($resultado = $calificaciones->resultados[$skill->id])

                <div class="row g-0 ms-5">
                    <div class="col" style="flex: 0 0 {{ $peso_actividades }}%;">{{ __('Activities') }}</div>
                    @if($peso_examenes>0)
                        <div style="flex: 0 0 {{ $peso_examenes }}%;">{{ __('Exams') }}</div>
                    @endif
                </div>

                <div class="progress-stacked ms-5">
                    @php($porcentaje_tarea = $resultado->porcentaje_tarea())
                    <div class="progress" role="progressbar"
                         style="width: {{ $peso_actividades * $porcentaje_tarea / 100 }}%"
                         aria-valuenow="{{ $porcentaje_tarea }}"
                         aria-valuemin="0" aria-valuemax="100"
                         title="{{ $resultado->puntos_tarea }}/{{ $resultado->puntos_totales_tarea }}">
                        <div class="progress-bar">
                            @if($porcentaje_tarea>=30){{ formato_decimales($porcentaje_tarea) }}&thinsp;%@endif
                        </div>
                    </div>
                    @if($resultado->puntos_totales_tarea > 0 && $porcentaje_tarea<30)
                        <div class="progress"
                             style="width:30%;">
                            <div class="progress-bar bg-body-secondary bg-opacity-10 text-body text-start ps-2">
                                {{ formato_decimales($porcentaje_tarea) }}&thinsp;%
                            </div>
                        </div>
                        <div class="progress"
                             style="width: {{ $peso_actividades * (100-$porcentaje_tarea) / 100 - 30 }}%">
                            <div class="progress-bar bg-body-secondary bg-opacity-10 text-body"></div>
                        </div>
                    @else
                        <div class="progress"
                             style="width: {{ $peso_actividades * (100-$porcentaje_tarea) / 100 }}%">
                            <div class="progress-bar bg-body-secondary bg-opacity-10 text-body"></div>
                        </div>
                    @endif

                    @if($peso_examenes>0)
                        @php($porcentaje_examen = $resultado->porcentaje_examen())
                        <div class="progress"
                             style="width: {{ $peso_examenes * $porcentaje_examen / 100 }}%"
                             aria-valuenow="{{ $porcentaje_examen }}" aria-valuemin="0" aria-valuemax="100"
                             title="{{ $resultado->puntos_examen }}/{{ $resultado->puntos_totales_examen }}">
                            <div class="progress-bar" role="progressbar">
                                @if($porcentaje_examen>=40){{ formato_decimales($porcentaje_examen) }}&thinsp;%@endif
                            </div>
                        </div>
                        @if($resultado->puntos_totales_examen> 0 && $porcentaje_examen<40)
                            <div class="progress"
                                 style="width:20%;">
                                <div class="progress-bar bg-body-secondary bg-opacity-10 text-body text-start ps-2">
                                    {{ formato_decimales($porcentaje_examen) }}&thinsp;%
                                </div>
                            </div>
                            <div class="progress"
                                 style="width: {{ $peso_examenes * (100-$porcentaje_examen) / 100 - 20 }}%">
                                <div class="progress-bar bg-body-secondary bg-opacity-10 text-body"></div>
                            </div>
                        @else
                            <div class="progress"
                                 style="width: {{ $peso_examenes * (100-$porcentaje_examen) / 100 }}%">
                                <div class="progress-bar bg-body-secondary bg-opacity-10 text-body"></div>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="row g-0 ms-5">
                    <div class="col text-secondary small" style="flex: 0 0 10%;">0&thinsp;%</div>
                    @if($peso_examenes>0)
                        <div class="col text-secondary small text-end pe-1 border-end"
                             style="flex: 0 0 {{ $peso_actividades-10 }}%;">{{ $peso_actividades }}&thinsp;%
                        </div>
                        <div class="col text-secondary small text-end"
                             style="flex: 0 0 {{ $peso_examenes }}%;">100&thinsp;%
                        </div>
                    @else
                        <div class="col text-secondary small text-end"
                             style="flex: 0 0 90%;">100&thinsp;%
                        </div>
                    @endif
                </div>

                <div class="row g-0 ms-5 mt-2">
                    <div class="col" style="flex: 0 0 {{ $peso_actividades }}%;">
                        <span>{{ __('Total of the skill') }}</span>
                    </div>
                </div>

                <div class="ms-5 progress">
                    @php($porcentaje_competencia = $resultado->porcentaje_competencia())
                    <div
                        class="progress-bar {{ $porcentaje_competencia < $calificaciones->minimo_competencias ? 'text-bg-warning' : 'text-bg-success' }}"
                        role="progressbar"
                        style="width: {{ $porcentaje_competencia }}%;"
                        aria-valuenow="{{ $porcentaje_competencia }}"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        title="{{ $resultado->tarea }}/{{ $resultado->actividad }}">
                        @if($porcentaje_competencia>=20){{ formato_decimales($porcentaje_competencia) }}&thinsp;%@endif
                    </div>
                    @if($resultado->actividad > 0 && $porcentaje_competencia<20)
                        <div class="progress-bar bg-body-secondary bg-opacity-10 text-body text-start ps-2">
                            {{ formato_decimales($porcentaje_competencia) }}&thinsp;%
                        </div>
                    @endif
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
