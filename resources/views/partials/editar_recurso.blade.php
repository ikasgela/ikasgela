@if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
    <a title="{{ __('Edit') }}"
       href="{{ route($ruta .'.edit', [$recurso->id]) }}"
       class='text-link-light'><i class="bi bi-pencil-square"></i></a>
@endif
