<span class="ms-2">
    @foreach($user->etiquetas() as $etiqueta)
        {{ html()->span($etiqueta)->class('badge bg-body-secondary text-body-secondary') }}
    @endforeach
</span>
