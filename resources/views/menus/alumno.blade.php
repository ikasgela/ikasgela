<li class="nav-title">{{ __('Student') }}</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('users.home') }}">
        <i class="nav-icon fas fa-home"></i> {{ __('Desktop') }}
        @if( session('num_actividades') > 0 )
            <span class="badge badge-danger">{{ session('num_actividades') }}</span>
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
    <a class="nav-link" href="{{ route('archivo.index') }}">
        <i class="nav-icon fas fa-archive"></i> {{ __('Archived') }}
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('results.index') }}">
        <i class="nav-icon fas fa-graduation-cap"></i> {{ __('Results') }}
    </a>
</li>
