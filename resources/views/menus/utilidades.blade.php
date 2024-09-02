@include('layouts.sidebar.nav-title', [
    'text' => __('Utilities'),
])
@include('layouts.sidebar.nav-item', [
    'route' => route('messages'),
    'text' => __('URL Shortener'),
    'icon' => 'bi-link',
])
