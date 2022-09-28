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
        <li class="c-sidebar-nav-item mt-auto"></li>
        @include('menus.lower')
    </ul>
    <div class="c-sidebar-footer text-center">&nbsp;</div>
</div>
