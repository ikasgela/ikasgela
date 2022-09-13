<li class="c-sidebar-nav-title">{{ __('Student') }}</li>
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('users.home') }}">
        <i class="c-sidebar-nav-icon fas fa-home"></i> {{ __('Desktop') }}
        @if( $alumno_actividades_asignadas > 0 )
            <span class="badge badge-danger">{{ $alumno_actividades_asignadas }}</span>
        @endif
    </a>
</li>
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('messages') }}">
        <i class="c-sidebar-nav-icon fas fa-comment"></i> {{ __('Tutorship') }}
        @if( Auth::user()->newThreadsCount() > 0 )
            <span class="badge badge-success">@include('messenger.unread-count')</span>
        @endif
    </a>
</li>
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('archivo.index') }}">
        <i class="c-sidebar-nav-icon fas fa-archive"></i> {{ __('Archived') }}
    </a>
</li>
@if(Auth::user()->curso_actual()?->progreso_visible && !Auth::user()->baja_ansiedad)
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('archivo.outline') }}">
            <i class="c-sidebar-nav-icon fas fa-list"></i> {{ __('Course progress') }}
        </a>
    </li>
@endif
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('results.index') }}">
        <i class="c-sidebar-nav-icon fas fa-graduation-cap"></i> {{ __('Results') }}
    </a>
</li>
