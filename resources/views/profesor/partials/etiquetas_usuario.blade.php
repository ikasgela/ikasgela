@foreach($user->etiquetas() as $etiqueta)
    {!! '<span class="badge badge-secondary ml-2">'.$etiqueta.'</span>' !!}
@endforeach
