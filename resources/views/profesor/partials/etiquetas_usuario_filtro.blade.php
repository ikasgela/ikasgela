<span class="ms-2">
    @foreach($user->etiquetas() as $etiqueta)
        {!! '<a class="badge bg-body-secondary text-body-secondary" href="'
                    . route(explode('.', Route::currentRouteName())[0] . '.index.filtro', ['tag_usuario' => $etiqueta])
                    . '">' . $etiqueta . '</a>' !!}
    @endforeach
</span>
