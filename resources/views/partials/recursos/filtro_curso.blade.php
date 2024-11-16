<div class="mb-3">
    {{ html()->form('POST', route($ruta))->open() }}
    @include('partials.desplegable_cursos')
    {{ html()->form()->close() }}
</div>
