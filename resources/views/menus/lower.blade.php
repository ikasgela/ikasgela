@auth
    @if(Auth::user()->hasRole('alumno'))
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('users.portada') }}">
                <i class="c-sidebar-nav-icon fas fa-clipboard-list"></i> {{ __('Courses') }}
            </a>
        </li>
    @endif
    @if(Auth::user()->hasAnyRole(['admin','profesor']))
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('settings.editar') }}">
                <i class="c-sidebar-nav-icon fas fa-cog"></i> {{ __('Settings') }}
            </a>
        </li>
    @endif
    @if(Auth::user()->hasRole('admin'))
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('logs') }}"
               target="_blank">
                <i class="c-sidebar-nav-icon fas fa-bug"></i> {{ __('Logs') }}
            </a>
        </li>
    @endif
@endauth
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="mailto:info@ikasgela.com">
        <i class="c-sidebar-nav-icon fas fa-envelope"></i> {{ __('Contact') }}
    </a>
</li>
@auth
    @if(Auth::user()->hasRole('admin'))
        <li class="c-sidebar-nav-title text-center text-lowercase">
            <span>{{ config('ikasgela.version') }} ({{ config('ikasgela.commit') }})</span>
        </li>
    @endif
@endauth
