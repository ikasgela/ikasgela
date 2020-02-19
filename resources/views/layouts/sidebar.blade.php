<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
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
            <li class="nav-item mt-auto">
                <a class="nav-link" href="mailto:info@ikasgela.com">
                    <i class="nav-icon fas fa-envelope"></i> {{ __('Contact') }}
                </a>
            </li>
            @auth
                @if(Auth::user()->hasAnyRole(['admin','profesor']))
                    {{--
                                        <li class="nav-item">
                                            <a class="nav-link nav-link-success" href="{{ url('/documentacion') }}">
                                                <i class="nav-icon fas fa-question-circle"></i> {{ __('Documentation') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link nav-link-danger" href="#">
                                                <i class="nav-icon fas fa-plus"></i> Premium
                                            </a>
                                        </li>
                    --}}
                @endif
                @if(Auth::user()->hasAnyRole(['admin','profesor']) || config('app.debug'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.editar') }}">
                            <i class="nav-icon fas fa-cog"></i> {{ __('Settings') }}
                        </a>
                    </li>
                @endif
                @if(Auth::user()->hasRole('admin'))
                    <li class="nav-title">{{ config('ikasgela.version') }} ({{ config('ikasgela.commit') }})</li>
                @endif
            @endauth
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
