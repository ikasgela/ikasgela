<div class="d-inline-flex align-items-center">
    <h2>{{ $actividad->nombre }}</h2>
    <div class="mb-2 ml-3">
        @foreach($actividad->etiquetas() as $etiqueta)
            {!! '<span class="badge badge-secondary">'.$etiqueta.'</span>' !!}
        @endforeach
    </div>
</div>
