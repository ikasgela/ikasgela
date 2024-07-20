@if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
    <a title="{{ __('Clear cache') }}"
       href="{{ route($ruta .'.borrar_cache', [$recurso->id]) }}"
       class='text-dark me-2'><i class="fas fa-broom"></i></a>
@endif
