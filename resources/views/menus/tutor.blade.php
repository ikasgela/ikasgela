@include('layouts.sidebar.nav-title', [
    'text' => __('Tutor'),
])
@include('layouts.sidebar.nav-item', [
    'route' => route('tutor.index'),
    'text' => __('Group report'),
    'icon' => 'bi-list-check',
])
{{--
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('tutor.tareas_enviadas') }}">
        <i class="c-sidebar-nav-icon fas fa-chart-bar"></i> {{ __('Activities per day') }}
    </a>
</li>
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon fas fa-user-check"></i> {{ __('Individual reports') }}
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('results.index') }}">
                <i class="c-sidebar-nav-icon fas fa-graduation-cap"></i> {{ __('Results') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('archivo.outline') }}">
                <i class="c-sidebar-nav-icon fas fa-list"></i> {{ __('Course progress') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('archivo.index') }}">
                <i class="c-sidebar-nav-icon fas fa-archive"></i> {{ __('Archived') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('archivo.diario') }}">
                <i class="c-sidebar-nav-icon fas fa-scroll"></i> {{ __('Activity journal') }}
            </a>
        </li>
    </ul>
</li>
--}}
