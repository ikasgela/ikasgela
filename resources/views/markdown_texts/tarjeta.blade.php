<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div><i class="fab fa-markdown mr-2"></i>{{ $markdown_text->titulo }}</div>
        <div>
            @include('partials.modificar_recursos', ['ruta' => 'markdown_texts'])
            @include('partials.editar_recurso', ['recurso' => $markdown_text, 'ruta' => 'markdown_texts'])
        </div>
    </div>
    <div class="card-body pb-1 line-numbers">
        {!! $texto !!}
    </div>
</div>

@include('partials.prismjs')
