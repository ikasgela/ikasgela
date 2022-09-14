@if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
    <a title="{{ __('Edit') }}"
       href="{{ route($ruta .'.edit', [$recurso->id]) }}"
       class='text-dark'><i class="fas fa-edit"></i></a>
@endif
