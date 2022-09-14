<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div><i class="fab fa-markdown mr-2"></i>{{ $markdown_text->titulo }}</div>
        @include('partials.editar_recurso', ['recurso' => $markdown_text, 'ruta' => 'markdown_texts'])
    </div>
    <div class="card-body pb-1 line-numbers">
        {!! $texto !!}
    </div>
</div>

@include('partials.prismjs')
