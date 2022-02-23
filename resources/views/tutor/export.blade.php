<div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
    <h1>{{ __('Group report') }}</h1>
    <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
</div>

@include('tutor.partials.tabla_usuarios', ['exportar' => true])

<div>
    <p class="text-center text-muted font-xs">{{ now()->isoFormat('L LTS') }}</p>
</div>
