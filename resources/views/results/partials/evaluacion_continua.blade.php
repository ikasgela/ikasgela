@include('partials.subtitulo', ['subtitulo' => __('Evaluation and calification')])

<div class="card-deck">
    @if($curso->minimo_entregadas > 0)
        <div
            class="card mb-3 {{ $calificaciones->examen_final ? 'bg-light text-dark' : ($calificaciones->actividades_obligatorias_superadas ? 'bg-success text-white' : 'bg-warning text-dark') }}">
            <div
                class="card-header">{{ __('Mandatory activities') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">{{ $calificaciones->num_actividades_obligatorias > 0 ? $calificaciones->actividades_obligatorias_superadas ? trans_choice('tasks.completed', 2) : ($calificaciones->numero_actividades_completadas+0)."/".($calificaciones->num_actividades_obligatorias+0)  : __('None') }}</p>
            </div>
        </div>
    @endif
    @if($calificaciones->minimo_competencias > 0)
        <div
            class="card mb-3 {{ $calificaciones->examen_final ? 'bg-light text-dark' : ($calificaciones->competencias_50_porciento ? 'bg-success text-white' : 'bg-warning text-dark') }}">
            <div class="card-header">{{ __('Skills') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">
                    {{ $calificaciones->competencias_50_porciento ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) }}</p>
            </div>
        </div>
    @endif
    @if($calificaciones->num_pruebas_evaluacion > 0)
        <div
            class="card mb-3 {{ ($curso->examenes_obligatorios || $calificaciones->examen_final) ? ($calificaciones->pruebas_evaluacion || $calificaciones->examen_final_superado) ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
            <div class="card-header">{{ __('Assessment tests') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">
                    {{ $calificaciones->num_pruebas_evaluacion > 0 ? ($calificaciones->pruebas_evaluacion || $calificaciones->examen_final_superado) ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) : __('None') }}</p>
            </div>
        </div>
    @endif
    <div
        class="card mb-3 {{ $calificaciones->examen_final ? 'bg-light text-dark' : ($calificaciones->evaluacion_continua_superada ? 'bg-success text-white' : 'bg-warning text-dark') }}">
        <div class="card-header">{{ __('Continuous evaluation') }}</div>
        <div class="card-body text-center">
            <p class="card-text"
               style="font-size:150%;">{{ $calificaciones->evaluacion_continua_superada ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</p>
        </div>
    </div>
    <div
        class="card mb-3 {{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado) ? 'bg-success text-white' : ($curso->disponible() ? 'bg-light text-dark' : 'bg-warning text-dark') }}">
        <div class="card-header">{{ __('Calification') }}</div>
        <div class="card-body text-center">
            <p class="card-text"
               style="font-size:150%;">{{ ($calificaciones->evaluacion_continua_superada || $calificaciones->examen_final_superado) ? $calificaciones->nota_final : ($curso->disponible() ? __('Unavailable') : __('Fail')) }}</p>
        </div>
    </div>
</div>
