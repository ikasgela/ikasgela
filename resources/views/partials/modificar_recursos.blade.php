@if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
    <a title="{{ __('Edit resources') }}"
       href="{{ route($ruta.'.actividad', [$actividad->id]) }}"
       class="text-dark me-2">
        <i class="fas fa-list"></i>
    </a>
@endif
