<header class="app-header navbar p-0">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="{{ url('/') }}">
        <img class="navbar-brand-full" src="{{ url('/svg/logo.svg') }}" height="25" alt="{{ __('Logo') }}">
        <img class="navbar-brand-minimized" src="{{ url('/svg/icono.svg') }}" width="30" alt="{{ __('Logo') }}">
    </a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    @if (Auth::check())
        <ul class="nav navbar-nav ml-auto mr-3">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <img class="img-avatar mx-1" src="{{Auth::user()->avatar_url()}}">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow mt-2">
                    <div class="px-3 py-2">
                        <h5>{{ Auth::user()->name }}</h5>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                    <a class="dropdown-item" href="/profile">
                        <i class="fas fa-user"></i> {{ __('Profile') }}
                    </a>
                    <div class="divider"></div>
                    <a class="dropdown-item" href="/password">
                        <i class="fas fa-key"></i> {{ __('Password') }}
                    </a>
                    <div class="divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                          style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    @else
        <ul class="nav navbar-nav ml-auto mr-3">
            <li class="nav-item mr-2"><a href="{{ route('login') }}">{{ __('Sign in') }}</a></li>
            <li class="nav-item"><a href="{{ route('register') }}">{{ __('Register') }}</a></li>
        </ul>
    @endif

</header>