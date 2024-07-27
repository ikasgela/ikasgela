@if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
    <a title="{{ __('Show') }}"
       href="{{ route($ruta .'.show', [$recurso->id]) }}"
       class='text-dark me-2'><i class="fas fa-eye"></i></a>
@endif
