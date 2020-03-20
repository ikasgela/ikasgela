<div id="sidebar" class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show">
    <div class="c-sidebar-brand">
        <img class="c-sidebar-brand-full c-d-dark-none"
             src="{{ url('/svg/logo-dark.svg') }}" height="25"
             alt="{{ __('Logo') }}">
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
                    @include('menus.tarjetas_prueba')
                @endif
            @endif
        @endauth
        <li class="c-sidebar-nav-item mt-auto">
            <a class="c-sidebar-nav-link" href="mailto:info@ikasgela.com">
                <i class="c-sidebar-nav-icon fas fa-envelope"></i> {{ __('Contact') }}
            </a>
        </li>
        @auth
            @if(Auth::user()->hasAnyRole(['admin','profesor']))
                {{--
                                    <li class="c-sidebar-nav-item">
                                        <a class="c-sidebar-nav-link nav-link-success" href="{{ url('/documentacion') }}">
                                            <i class="c-sidebar-nav-icon fas fa-question-circle"></i> {{ __('Documentation') }}
                                        </a>
                                    </li>
                                    <li class="c-sidebar-nav-item">
                                        <a class="c-sidebar-nav-link nav-link-danger" href="#">
                                            <i class="c-sidebar-nav-icon fas fa-plus"></i> Premium
                                        </a>
                                    </li>
                --}}
            @endif
            @if(Auth::user()->hasAnyRole(['admin','profesor']) || config('app.debug'))
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link" href="{{ route('settings.editar') }}">
                        <i class="c-sidebar-nav-icon fas fa-cog"></i> {{ __('Settings') }}
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasRole('admin'))
                <li class="c-sidebar-nav-title text-center">
                    <span>{{ config('ikasgela.version') }} ({{ config('ikasgela.commit') }})</span>
                </li>
            @endif
        @endauth
    </ul>
    <div class="c-sidebar-footer text-center">&nbsp;</div>
</div>
