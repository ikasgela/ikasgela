@include('layouts.sidebar.nav-title', [
    'text' => __('Tests'),
])
@include('layouts.sidebar.nav-item-desplegable', [
    'route' => route('lw.livewire'),
    'text' => __('Livewire'),
])
@include('layouts.sidebar.nav-item-desplegable', [
    'route' => route('lw.lista_tareas'),
    'text' => __('Desktop'),
])
@include('layouts.sidebar.nav-item-desplegable', [
    'route' => route('lw.tarjeta_intellij'),
    'text' => __('IntelliJ project'),
])
