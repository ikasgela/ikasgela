<div id="sidebar"
     class="c-sidebar c-sidebar-dark c-sidebar-fixed {{ !session('sidebar_open') ? 'c-sidebar-lg-show' : '' }}">
    <div class="c-sidebar-brand {{ config('app.debug') ? 'bg-warning' : 'bg-primary c-header-dark' }}">
        <a href="{{ url('/') }}">
            <img class="c-sidebar-brand-full c-d-dark-none"
                 src="{{ config('app.debug') ? url('/svg/logo-black.svg') : url('/svg/logo-dark.svg') }}" height="25"
                 alt="{{ __('Logo') }}">
        </a>
    </div>
    <ul class="c-sidebar-nav">
        @auth
            @if(Auth::user()->hasRole('alumno'))
                @include('menus.alumno')
            @endif
        @endauth
        @auth
            @if(Auth::user()->hasRole('profesor'))
                @include('menus.profesor')
            @endif
        @endauth
        @auth
            @if(Auth::user()->hasRole('tutor'))
                @include('menus.tutor')
            @endif
        @endauth
        @auth
            @if(Auth::user()->hasRole('admin'))
                @include('menus.admin')
                @if(config('app.debug'))
                    {{-- @include('menus.pruebas') --}}
                @endif
            @endif
        @endauth
        @auth
            @if(Auth::user()->hasRole('alumno'))
                <li class="c-sidebar-nav-item mt-auto">
                    <a class="c-sidebar-nav-link" href="{{ route('users.portada') }}">
                        <i class="c-sidebar-nav-icon fas fa-clipboard-list"></i> {{ __('Courses') }}
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasAnyRole(['admin','profesor']))
                <li class="c-sidebar-nav-item mt-auto">
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
        <li class="c-sidebar-nav-item {{ Auth::user() == null ? 'mt-auto' : '' }}">
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
    </ul>
    <div class="c-sidebar-footer text-center">&nbsp;</div>
</div>
