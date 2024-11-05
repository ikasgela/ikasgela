@include('layouts.sidebar.nav-title', [
    'text' => __('Ikasgela'),
])
@include('layouts.sidebar.nav-item', [
    'route' => route('users.portada'),
    'text' => __('Courses'),
    'icon' => 'bi-collection',
])
