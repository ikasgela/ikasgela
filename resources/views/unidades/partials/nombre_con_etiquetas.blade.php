<span class="mr-2">{{ $unidad->nombre }}</span>
@foreach($unidad->etiquetas() as $etiqueta)
    @if(!isset($pdf) || !$pdf)
        {!! '<span class="badge badge-secondary">'.$etiqueta.'</span>' !!}
    @else
        {!! '<span>('.$etiqueta.')</span>' !!}
    @endif
@endforeach
