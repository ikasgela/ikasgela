<span class="ms-2">
    @foreach($user->etiquetas() as $etiqueta)
        {!! '<a class="badge badge-secondary" href="'
                    . route(explode('.', Route::currentRouteName())[0] . '.index.filtro', ['tag_usuario' => $etiqueta])
                    . '">' . $etiqueta . '</a>' !!}
    @endforeach
</span>
