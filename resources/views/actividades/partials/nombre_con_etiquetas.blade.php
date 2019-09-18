<span class="mr-2">{{ $actividad->nombre }}</span>
@foreach($actividad->etiquetas() as $etiqueta)
    {!! '<span class="badge badge-secondary">'.$etiqueta.'</span>' !!}
@endforeach
