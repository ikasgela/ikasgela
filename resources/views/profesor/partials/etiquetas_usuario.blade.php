<span class="ms-2">
    @foreach($user->etiquetas() as $etiqueta)
        {!! '<span class="badge bg-body-secondary text-body-secondary">'.$etiqueta.'</span>' !!}
    @endforeach
</span>
