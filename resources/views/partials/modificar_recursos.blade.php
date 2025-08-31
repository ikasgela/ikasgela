@if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
    <a title="{{ __('Edit resources') }}"
       href="{{ route($ruta.'.actividad', [$actividad->id]) }}"
       class="text-link-light me-2">
        <i class="bi bi-puzzle"></i>
    </a>
@endif
