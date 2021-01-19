@include('partials.subtitulo', ['subtitulo' => __('Evaluation and calification')])

<div class="card-deck">
    @if($curso->minimo_entregadas > 0)
        <div class="card mb-3 {{ $actividades_obligatorias_fondo }}">
            <div class="card-header">{{ __('Mandatory activities') }}</div>
            <div class="card-body text-center">
                <p class="card-text" style="font-size:150%;">{{ $actividades_obligatorias_dato }}</p>
            </div>
        </div>
    @endif
    @if($calificaciones->minimo_competencias > 0)
        <div class="card mb-3 {{ $competencias_fondo }}">
            <div class="card-header">{{ __('Skills') }}</div>
            <div class="card-body text-center">
                <p class="card-text" style="font-size:150%;">{{ $competencias_dato }}</p>
            </div>
        </div>
    @endif
    @if($calificaciones->num_pruebas_evaluacion > 0)
        <div class="card mb-3 {{ $pruebas_evaluacion_fondo }}">
            <div class="card-header">{{ __('Assessment tests') }}</div>
            <div class="card-body text-center">
                <p class="card-text" style="font-size:150%;">{{ $pruebas_evaluacion_dato }}</p>
            </div>
        </div>
    @endif
    <div class="card mb-3 {{ $evaluacion_continua_fondo }}">
        <div class="card-header">{{ __('Continuous evaluation') }}</div>
        <div class="card-body text-center">
            <p class="card-text" style="font-size:150%;">{{ $evaluacion_continua_dato }}</p>
        </div>
    </div>
    <div class="card mb-3 {{ $calificacion_fondo }}">
        <div class="card-header">{{ __('Calification') }}</div>
        <div class="card-body text-center">
            <p class="card-text" style="font-size:150%;">{{ $calificacion_dato }}</p>
        </div>
    </div>
</div>
