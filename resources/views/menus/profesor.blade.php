<li class="c-sidebar-nav-title">{{ __('Teacher') }}</li>

<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('profesor.index') }}">
        <i class="c-sidebar-nav-icon fas fa-tasks"></i> {{ __('Control panel') }}
        @if( session('num_enviadas') > 0 )
            <span class="badge badge-danger">{{ session('num_enviadas') }}</span>
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
    <a class="c-sidebar-nav-link" href="{{ route('teams.index') }}">
        <i class="c-sidebar-nav-icon fas fa-users"></i> {{ __('Teams') }}
    </a>
</li>
