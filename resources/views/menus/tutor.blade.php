<li class="c-sidebar-nav-title">{{ __('Tutor') }}</li>
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('tutor.index') }}">
        <i class="c-sidebar-nav-icon fas fa-tasks"></i> {{ __('Group report') }}
    </a>
</li>
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('results.index') }}">
        <i class="c-sidebar-nav-icon fas fa-graduation-cap"></i> {{ __('Results') }}
    </a>
</li>
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('tutor.tareas_enviadas') }}">
        <i class="c-sidebar-nav-icon fas fa-chart-bar"></i> {{ __('Activities per day') }}
    </a>
</li>
