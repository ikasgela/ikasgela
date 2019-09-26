<header class="app-header navbar p-0 {{ config('app.debug') ? 'bg-warning' : 'bg-primary navbar-dark' }}">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="{{ url('/') }}">@include('partials.logos')</a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none mr-auto" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    @if(Auth::check())
        <ul class="nav navbar-nav ml-auto mr-3">
            <li class="{{ config('app.debug') ? 'text-dark' : '' }} mr-2 d-sm-down-none">{{ Auth::user()->name }}</li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" role="button" title="{{ __('Settings') }}"
                   aria-haspopup="true"
                   aria-expanded="false">
                    <img alt="Avatar" style="width:35px;" src="{{Auth::user()->avatar_url(70)}}">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow mt-2 pb-2">
                    <div class="dropdown-item-text">
                        <h5>{{ Auth::user()->name }}</h5>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    @include('layouts.partials.toggle_help')
                    <div class="dropdown-divider"></div>
                    @include('layouts.partials.toggle_notifications')
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
