<div class="card">
    <div class="card-header"><i class="fab fa-markdown"></i> {{ $markdown_text->titulo }}</div>
    <div class="card-body pb-1 line-numbers">
        {!! $texto !!}
    </div>
</div>

@include('partials.prismjs')
