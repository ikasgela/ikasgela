<div class="d-flex align-items-center">
    <span class="me-2">{{ $unidad->nombre }}</span>
    @foreach($unidad->etiquetas() as $etiqueta)
        @if(!isset($pdf) || !$pdf)
            {!! '<span class="badge bg-body-secondary text-body-secondary">'.$etiqueta.'</span>' !!}
        @else
            {!! '<span>('.$etiqueta.')</span>' !!}
        @endif
    @endforeach
</div>
