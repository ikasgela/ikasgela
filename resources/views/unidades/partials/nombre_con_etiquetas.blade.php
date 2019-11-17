<span class="mr-2">{{ $unidad->nombre }}</span>
@foreach($unidad->etiquetas() as $etiqueta)
    {!! '<span class="badge badge-secondary">'.$etiqueta.'</span>' !!}
@endforeach
