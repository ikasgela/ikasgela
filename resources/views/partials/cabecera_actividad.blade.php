<div class="row">
    <div class="col-md-12">
        {{-- Tarjeta --}}
        <div class="card">
            <div class="card-header">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</div>
            <div class="card-body pb-1">
                <h2>{{ $actividad->nombre }}</h2>
                <p>{{ $actividad->descripcion }}</p>
            </div>
        </div>
        {{-- Fin tarjeta--}}
    </div>
</div>
