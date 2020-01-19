<li class="nav-title">{{ __('Student') }}</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('users.home') }}">
        <i class="nav-icon fas fa-home"></i> {{ __('Desktop') }}
        @if( $alumno_actividades_asignadas > 0 )
            <span class="badge badge-danger">{{ $alumno_actividades_asignadas }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('messages') }}">
        <i class="nav-icon fas fa-comment"></i> {{ __('Tutorship') }}
        @if( Auth::user()->newThreadsCount() > 0 )
            <span class="badge badge-success">@include('messenger.unread-count')</span>
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
@if(config('app.debug'))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('users.portada') }}">
            <i class="nav-icon fas fa-clipboard-list"></i> {{ __('Portada') }}
        </a>
    </li>
@endif
