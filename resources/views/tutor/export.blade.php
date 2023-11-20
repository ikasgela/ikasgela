<div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
    <h3>{{ __('Group report') }}</h3>
    @isset($curso)
        <h5>{{ $curso->pretty_name }}</h5>
    @endisset
    @isset($milestone)
        <h5>{{ $milestone->name . ' (' . $milestone->date->isoFormat('L LT') . ')' }}</h5>
    @endisset
    <p></p>
</div>

@include('tutor.partials.tabla_usuarios', ['exportar' => true])

<div>
    <p>{{ now()->isoFormat('L LTS') }}</p>
</div>
