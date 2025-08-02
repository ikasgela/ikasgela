<div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
        <div><i class="bi bi-markdown me-2"></i>{{ $markdown_text->titulo }}</div>
        <div>
            @include('partials.borrar_cache_recurso', ['recurso' => $markdown_text, 'ruta' => 'markdown_texts'])
            @include('partials.modificar_recursos', ['ruta' => 'markdown_texts'])
            @include('partials.editar_recurso', ['recurso' => $markdown_text, 'ruta' => 'markdown_texts'])
        </div>
    </div>
    <div class="card-body pb-0 line-numbers">
        {!! $texto !!}
    </div>
</div>

@include('partials.prismjs')
