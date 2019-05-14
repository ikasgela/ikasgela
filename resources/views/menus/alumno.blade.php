<li class="nav-title">{{ __('Student') }}</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('users.home') }}">
        <i class="nav-icon fas fa-home"></i> {{ __('Desktop') }}
        <span class="badge badge-danger">{{ session('num_actividades') }}</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('messages') }}">
        <i class="nav-icon fas fa-comment"></i> {{ __('Tutorship') }}
        <span class="badge badge-danger">@include('messenger.unread-count')</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('archivo.index') }}">
        <i class="nav-icon fas fa-archive"></i> {{ __('Archived') }}
    </a>
</li>
@if(config('app.debug'))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('results.index') }}">
            <i class="nav-icon fas fa-graduation-cap"></i> {{ __('Results') }}
        </a>
    </li>
@endif
