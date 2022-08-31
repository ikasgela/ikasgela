@if(isset($actividad) && $recurso->pivote($actividad)->titulo_visible || Route::currentRouteName() == $ruta . '.show')
    <h5 class="card-title">{{ $recurso->titulo }}</h5>
@endif
@if(isset($actividad) && $recurso->pivote($actividad)->descripcion_visible || Route::currentRouteName() == $ruta . '.show')
    <p class="card-text">{{ $recurso->descripcion }}</p>
@endif
