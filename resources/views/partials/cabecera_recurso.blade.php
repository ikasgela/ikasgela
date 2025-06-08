@isset($actividad)
    @php($pivote = $recurso->pivote($actividad))
@endisset
@if(isset($pivote) && $pivote->titulo_visible || Route::currentRouteName() == $ruta . '.show')
    <h5 class="card-title">{{ $recurso->titulo }}</h5>
@endif
@if(isset($pivote) && $pivote->descripcion_visible || Route::currentRouteName() == $ruta . '.show')
    <p class="card-text">{{ $recurso->descripcion }}</p>
@endif
