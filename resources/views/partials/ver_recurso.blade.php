@if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
    <a title="{{ __('Show') }}"
       href="{{ route($ruta .'.show', [$recurso->id]) }}"
       class='text-link-light me-2'><i class="bi bi-eye"></i></a>
@endif
