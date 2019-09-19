<li class="nav-title">{{ __('Teacher') }}</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('profesor.index') }}">
        <i class="nav-icon fas fa-tasks"></i> {{ __('Control panel') }}
        @if( session('num_enviadas') > 0 )
            <span class="badge badge-danger">{{ session('num_enviadas') }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('messages') }}">
        <i class="nav-icon fas fa-comment"></i> {{ __('Tutorship') }}
        @if( Auth::user()->newThreadsCount() > 0 )
            <span class="badge badge-danger">@include('messenger.unread-count')</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('results.index') }}">
        <i class="nav-icon fas fa-graduation-cap"></i> {{ __('Results') }}
    </a>
</li>
