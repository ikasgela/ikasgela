<div class="d-inline-flex align-items-center">
    <h2>{{ $actividad->nombre }}</h2>
    <div class="mb-2 ml-3">
        @include('partials.etiquetas', ['etiquetas' => $actividad->etiquetas()])
    </div>
</div>
