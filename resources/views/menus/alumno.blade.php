@include('layouts.sidebar.nav-title', [
    'text' => __('Student'),
])
@include('layouts.sidebar.nav-item', [
    'route' => route('users.home'),
    'text' => __('Desktop'),
    'icon' => 'bi-house',
    'badge_number' => $alumno_actividades_asignadas,
    'badge_color' => 'danger',
])
@include('layouts.sidebar.nav-item', [
    'route' => route('messages'),
    'text' => __('Tutorship'),
    'icon' => 'bi-chat',
    'badge_number' => Auth::user()->newThreadsCount(),
    'badge_color' => 'success',
])
@if(Auth::user()->curso_actual()?->mostrar_calificaciones)
    @include('layouts.sidebar.nav-item', [
        'route' => route('results.index'),
        'text' => __('Results'),
        'icon' => 'bi-mortarboard',
    ])
@endif
@include('layouts.sidebar.nav-title', [
    'text' => __('Course'),
])
@include('layouts.sidebar.nav-item', [
    'route' => route('archivo.index'),
    'text' => __('Archived'),
    'icon' => 'bi-archive',
])
@if(Auth::user()->curso_actual()?->mostrar_calificaciones && Auth::user()->curso_actual()?->progreso_visible && !Auth::user()->baja_ansiedad)
    @include('layouts.sidebar.nav-item', [
        'route' => route('archivo.outline'),
        'text' => __('Progress'),
        'icon' => 'bi-graph-up',
    ])
@endif
