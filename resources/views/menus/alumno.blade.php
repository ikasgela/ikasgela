@include('layouts.sidebar.nav-title', [
    'text' => __('Student'),
])
@include('layouts.sidebar.nav-item', [
    'route' => route('users.home'),
    'text' => __('Desktop'),
    'icon' => 'bi-house',
])
{{--
@if( $alumno_actividades_asignadas > 0 )
    <span class="badge badge-danger">{{ $alumno_actividades_asignadas }}</span>
@endif
--}}
@include('layouts.sidebar.nav-item', [
    'route' => route('messages'),
    'text' => __('Tutorship'),
    'icon' => 'bi-chat',
])
{{--
@if( Auth::user()->newThreadsCount() > 0 )
    <span class="badge badge-success">@include('messenger.unread-count')</span>
@endif
--}}
@include('layouts.sidebar.nav-item', [
    'route' => route('archivo.index'),
    'text' => __('Archived'),
    'icon' => 'bi-archive',
])
@if(Auth::user()->curso_actual()?->progreso_visible && !Auth::user()->baja_ansiedad)
    @include('layouts.sidebar.nav-item', [
        'route' => route('archivo.outline'),
        'text' => __('Course progress'),
        'icon' => 'bi-list-task',
    ])
@endif
@include('layouts.sidebar.nav-item', [
    'route' => route('results.index'),
    'text' => __('Results'),
    'icon' => 'bi-mortarboard',
    'last' => true,
])
