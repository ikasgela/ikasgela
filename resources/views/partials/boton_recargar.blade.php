@if(Auth::user()->hasRole('alumno'))
    <a href="{{ route('users.home') }}" class="btn btn-primary ms-3">
        <i class="fas fa-redo me-1"></i> {{ __('Reload') }}
    </a>
@elseif(Auth::user()->hasRole('profesor'))
    <a href="{{ route('profesor.index') }}" class="btn btn-primary ms-3">
        <i class="fas fa-redo me-1"></i> {{ __('Reload') }}
    </a>
@endif
