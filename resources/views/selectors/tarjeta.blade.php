<div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
        <div><i class="bi bi-shuffle me-2"></i>{{ __('Selector') }}</div>
        <div>
            @include('partials.ver_recurso', ['recurso' => $selector, 'ruta' => 'selectors'])
            @include('partials.modificar_recursos', ['ruta' => 'selectors'])
            @include('partials.editar_recurso', ['recurso' => $selector, 'ruta' => 'selectors'])
        </div>
    </div>
    <div class="card-body">
        @include('partials.cabecera_recurso', ['recurso' => $selector, 'ruta' => 'selectors'])

        @include('rule_groups.tabla', ['rule_groups' => $selector->rule_groups, 'selector' => $selector])
    </div>
</div>
