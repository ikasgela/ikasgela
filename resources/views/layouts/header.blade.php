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
            @auth
                @if(Auth::user()->isImpersonated())
                    <li class="c-header-nav-item mr-3">
                        <div class='btn-group'>
                            <a title="{{ __('Leave impersonation') }}"
                               href="{{ route('impersonate.leave') }}"
                               class='btn btn-light btn-sm '><i class="fas fa-user-secret text-danger"></i></a>
                        </div>
                    </li>
                @endif
            @endauth
            <li class="{{ config('app.debug') ? 'text-dark' : 'text-light' }} mr-2 d-sm-down-none">{{ Auth::user()->full_name }}</li>
            <li class="c-header-nav-item dropdown">
                <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" title="{{ __('Settings') }}"
                   aria-haspopup="true"
                   aria-expanded="false">
                    @include('users.partials.avatar', ['user' => Auth::user(), 'width' => 35])
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow mt-2 pb-2">
                    <div class="dropdown-item-text">
                        <h5>{{ Auth::user()->full_name }}</h5>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    @include('layouts.partials.toggle_help')
                    @if(Auth::user()->hasAnyRole(['alumno','profesor']))
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('notifications.edit') }}">
                        <span class="text-center ml-n2 mr-1" style="width: 1.5rem;">
                            <i class="fas fa-bell text-primary"></i>
                        </span> {{ __('Notification settings') }}
                        </a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ url('/profile') }}">
                        <span class="text-center ml-n2 mr-1" style="width: 1.5rem;">
                            <i class="fas fa-user text-primary"></i>
                        </span> {{ __('Profile') }}
                    </a>
                    <a class="dropdown-item" href="{{ url('/password') }}">
                        <span class="text-center ml-n2 mr-1" style="width: 1.5rem;">
                        <i class="fas fa-key text-primary"></i>
                        </span> {{ __('Password') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="text-center ml-n2 mr-1" style="width: 1.5rem;">
                            <i class="fas fa-sign-out-alt"></i>
                        </span> {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    @else
        <div class="c-header-nav ml-auto" style="width:50px;">
        </div>
    @endif

</header>
