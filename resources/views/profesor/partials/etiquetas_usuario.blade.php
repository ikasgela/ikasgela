<span class="ml-2">
    @foreach($user->etiquetas() as $etiqueta)
        {!! '<span class="badge badge-secondary">'.$etiqueta.'</span>' !!}
    @endforeach
</span>
