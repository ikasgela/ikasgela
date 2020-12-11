@include('partials.subtitulo', ['subtitulo' => __('Continuous evaluation')])

<div class="card-deck">
    @if($curso->minimo_entregadas > 0)
        <div
            class="card mb-3 {{ $actividades_obligatorias_superadas ? 'bg-success text-white' : 'bg-warning text-dark' }}">
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
            class="card mb-3 {{ $competencias_50_porciento ? 'bg-success text-white' : 'bg-warning text-dark' }}">
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
            class="card mb-3 {{ $curso->examenes_obligatorios ? $pruebas_evaluacion ? 'bg-success text-white' : 'bg-warning text-dark' : 'bg-light text-dark' }}">
            <div class="card-header">{{ __('Assessment tests') }}</div>
            <div class="card-body text-center">
                <p class="card-text"
                   style="font-size:150%;">
                    {{ $num_pruebas_evaluacion > 0 ? $pruebas_evaluacion ? trans_choice('tasks.passed', 2) : trans_choice('tasks.not_passed', 2) : __('None') }}</p>
            </div>
        </div>
    @endif
    <div
        class="card mb-3 {{ ($actividades_obligatorias_superadas || $num_actividades_obligatorias == 0 || $curso->minimo_entregadas == 0)
                && (!$curso->examenes_obligatorios || $pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento && $nota_final >= 5 ? 'bg-success text-white' : 'bg-warning text-dark' }}">
        <div class="card-header">{{ __('Continuous evaluation') }}</div>
        <div class="card-body text-center">
            <p class="card-text"
               style="font-size:150%;">{{ ($actividades_obligatorias_superadas || $num_actividades_obligatorias == 0 || $curso->minimo_entregadas == 0)
                && (!$curso->examenes_obligatorios || $pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento && $nota_final >= 5 ? trans_choice('tasks.passed', 1) : trans_choice('tasks.not_passed', 1) }}</p>
        </div>
    </div>
    <div class="card mb-3 bg-light text-dark">
        <div class="card-header">{{ __('Calification') }}</div>
        <div class="card-body text-center">
            <p class="card-text"
               style="font-size:150%;">{{ ($actividades_obligatorias_superadas || $num_actividades_obligatorias == 0)
                && (!$curso->examenes_obligatorios || $pruebas_evaluacion || $num_pruebas_evaluacion == 0)
                && $competencias_50_porciento ? $nota_final : ($curso->disponible() ? __('Unavailable') : __('Fail')) }}</p>
        </div>
    </div>
</div>
