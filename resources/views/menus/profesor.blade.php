@include('layouts.sidebar.nav-title', [
    'text' => __('Teacher'),
])
@include('layouts.sidebar.nav-item', [
    'route' => route('profesor.index'),
    'text' => __('Control panel'),
    'icon' => 'bi-house',
    'badge_number' => $profesor_actividades_pendientes,
    'badge_color' => 'danger',
])
@include('layouts.sidebar.nav-item', [
    'route' => route('messages'),
    'text' => __('Tutorship'),
    'icon' => 'bi-chat',
    'badge_number' => Auth::user()->newThreadsCount(),
    'badge_color' => 'success',
])
@include('layouts.sidebar.nav-item', [
    'route' => route('teams.index'),
    'text' => __('Teams'),
    'icon' => 'bi-people',
])
