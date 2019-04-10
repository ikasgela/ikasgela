<header class="app-header navbar p-0 {{ config('app.debug') ? 'bg-warning' : '' }}">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    @if(Auth::check())
        <a class="navbar-brand" href="{{ url('/') }}">@include('partials.logos')</a>
    @else
        <a class="navbar-brand" href="{{ url('/') }}">@include('partials.logos')</a>
    @endif
    <button class="navbar-toggler sidebar-toggler d-md-down-none mr-auto" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    @if(Auth::check())
        <ul class="nav navbar-nav ml-auto mr-3">
            @if(config('app.debug'))
                {{-- Mensajes --}}
                <li class="nav-item dropdown pt-1">
                    <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true">
                        <i class="fas fa-envelope"></i>
                        <span class="badge badge-pill badge-danger">7</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                        <div class="dropdown-header text-center">
                            <strong>You have 4 messages</strong>
                        </div>
                        <a class="dropdown-item" href="#">
                            <div class="message">
                                <div class="py-3 mr-3 float-left">
                                    <div class="avatar">
                                        <img class="img-avatar" src="{{ Auth::user()->avatar_url() }}"
                                             alt="admin@bootstrapmaster.com">
                                        <span class="avatar-status badge-success"></span>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">John Doe</small>
                                    <small class="text-muted float-right mt-1">Just now</small>
                                </div>
                                <div class="text-truncate font-weight-bold">
                                    <span class="fa fa-exclamation text-danger"></span> Important message
                                </div>
                                <div class="small text-muted text-truncate">Lorem ipsum dolor sit amet, consectetur
                                    adipisicing elit, sed do eiusmod tempor incididunt...
                                </div>
                            </div>
                        </a>
                        <a class="dropdown-item text-center" href="#">
                            <strong>View all messages</strong>
                        </a>
                    </div>
                </li>
                {{-- Mensajes --}}
            @endif
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="true"
                   aria-expanded="false">
                    <img class="img-avatar mx-1" src="{{Auth::user()->avatar_url()}}">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow mt-2 pb-2">
                    <div class="dropdown-item-text">
                        <h5>{{ Auth::user()->name }}</h5>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('users.toggle_help') }}"
                       onclick="event.preventDefault(); document.getElementById('toggle_help').submit();">
                        @if(session('tutorial'))
                            <i class="fas fa-check text-success"></i>
                        @else
                            <i class="fas fa-times"></i>
                        @endif
                        {{ __('View tutorial') }}
                    </a>
                    <form id="toggle_help" action="{{ route('users.toggle_help') }}" method="POST"
                          style="display: none;">
                        @csrf
                    </form>
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