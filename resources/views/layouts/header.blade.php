<header class="c-header c-header-fixed {{ config('app.debug') ? 'bg-warning' : 'bg-primary c-header-dark' }}">

    <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
            data-class="c-sidebar-show">
        <span class="c-header-toggler-icon"></span>
    </button>
    <a class="c-header-brand d-lg-none" href="{{ url('/') }}">
        @include('partials.logos')
    </a>
    <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
            data-class="c-sidebar-lg-show" responsive="true">
        <span class="c-header-toggler-icon"></span>
    </button>

    @if(Auth::check())
        <ul class="c-header-nav ml-auto mr-3">
            <li class="{{ config('app.debug') ? 'text-dark' : 'text-light' }} mr-2 d-sm-down-none">{{ Auth::user()->name }}</li>
            <li class="c-header-nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" role="button" title="{{ __('Settings') }}"
                   aria-haspopup="true"
                   aria-expanded="false">
                    <img alt="Avatar" style="width:35px;" src="{{Auth::user()->avatar_url(70)}}"
                         onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow mt-2 pb-2">
                    <div class="dropdown-item-text">
                        <h5>{{ Auth::user()->name }}</h5>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    @include('layouts.partials.toggle_help')
                    @if(Auth::user()->hasAnyRole(['alumno','profesor']))
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('notifications.edit') }}">
                            <i class="fas fa-bell text-primary"></i> {{ __('Notification settings') }}
                        </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/profile">
                        <i class="fas fa-user text-primary"></i> {{ __('Profile') }}
                    </a>
                    <a class="dropdown-item" href="/password">
                        <i class="fas fa-key text-primary"></i> {{ __('Password') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    @else
        {{--
                <ul class="nav navbar-nav ml-auto mr-3">
                    <li class="nav-item mr-2"><a href="{{ route('login') }}">{{ __('Sign in') }}</a></li>
                    <li class="nav-item"><a href="{{ route('register') }}">{{ __('Register') }}</a></li>
                </ul>
        --}}
    @endif

</header>
