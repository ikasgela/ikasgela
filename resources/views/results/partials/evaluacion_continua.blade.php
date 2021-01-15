@include('partials.subtitulo', ['subtitulo' => __('Evaluation and calification')])

<div class="card-deck">
    @if($curso->minimo_entregadas > 0)
        <div
            class="card mb-3 {{ $examen_final ? 'bg-light text-dark' : ($actividades_obligatorias_superadas ? 'bg-success text-white' : 'bg-warning text-dark') }}">
            <div
                class="card-header">{{ __('Mandatory activities') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">{{ $num_actividades_obligatorias > 0 ? $actividades_obligatorias_superadas ? trans_choice('tasks.completed', 2) : ($numero_actividades_completadas+0)."/".($num_actividades_obligatorias+0)  : __('None') }}</p>
            </div>
        </div>
    @endif
    @if($minimo_competencias > 0)
        <div
            class="card mb-3 {{ $examen_final ? 'bg-light text-dark' : ($competencias_50_porciento ? 'bg-success text-white' : 'bg-warning text-dark') }}">
            <div class="card-header">{{ __('Skills') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">
                    {{ $competencias_50_porciento ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) }}</p>
            </div>
        </div>
    @endif
    @if($num_pruebas_evaluacion > 0)
        <div
            class="card mb-3 {{ ($curso->examenes_obligatorios || $examen_final) ? ($pruebas_evaluacion || $examen_final_superado) ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
            <div class="card-header">{{ __('Assessment tests') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">
                    {{ $num_pruebas_evaluacion > 0 ? ($pruebas_evaluacion || $examen_final_superado) ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) : __('None') }}</p>
            </div>
        </div>
    @endif
    <div
        class="card mb-3 {{ $examen_final ? 'bg-light text-dark' : ($evaluacion_continua_superada ? 'bg-success text-white' : 'bg-warning text-dark') }}">
        <div class="card-header">{{ __('Continuous evaluation') }}</div>
        <div class="card-body text-center">
            <p class="card-text"
               style="font-size:150%;">{{ $evaluacion_continua_superada ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</p>
        </div>
    </div>
    <div class="card mb-3 bg-light text-dark">
        <div class="card-header">{{ __('Calification') }}</div>
        <div class="card-body text-center">
            <p class="card-text"
               style="font-size:150%;">{{ ($evaluacion_continua_superada || $examen_final_superado) ? $nota_final : ($curso->disponible() ? __('Unavailable') : __('Fail')) }}</p>
        </div>
    </div>
</div>
