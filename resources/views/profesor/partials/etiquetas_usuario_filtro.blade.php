<span class="ms-2">
    @foreach($user->etiquetas() as $etiqueta)
        {{ html()
            ->a(route(explode('.', Route::currentRouteName())[0] . '.index.filtro', ['tag_usuario' => $etiqueta]), $etiqueta)
            ->class('badge bg-body-secondary text-body-secondary') }}
    @endforeach
</span>
