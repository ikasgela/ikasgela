<div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
    <h1>{{ $titular }}</h1>
    @if(isset($subtitulo))
        <h2 class="text-muted font-xl">{{ $subtitulo }}</h2>
    @else
        <h2 class="text-muted font-xl">{{ Auth::user()->curso_actual()?->full_name }}</h2>
    @endif
</div>
