@use(Illuminate\Support\Str)
@if(Auth::user()->hasAnyRole(['admin','profesor'])
    && (Route::currentRouteName() == 'actividades.preview' || Str::endsWith(Route::currentRouteName(), '.show')))
    <a title="{{ __('Clear cache') }}"
       href="{{ route($ruta .'.borrar_cache', [$recurso->id]) }}"
       class='text-link-light me-2'><i class="fas fa-broom"></i></a>
@endif
