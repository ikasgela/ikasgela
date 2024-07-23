<li class="nav-item dropdown">
    <a id="navbarDropdown"
       class="nav-link dropdown-toggle text-{{ $debug_text_color }} d-flex align-items-center" href="#"
       role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="me-1">{{ Auth::user()->name }}</span>
        @if(config('ikasgela.avatar_enabled'))
            <span class="mx-2">
                @include('users.partials.avatar', ['user' => Auth::user(), 'width' => 35])
            </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
        <div class="dropdown-item-text">
            <h5>{{ Auth::user()->full_name }}</h5>
            <small class="text-body-tertiary">{{ Auth::user()->email }}</small>
        </div>
        <div class="dropdown-divider"></div>
        @include('layouts.partials.toggle_help')
        @if(Auth::user()->hasAnyRole(['alumno','profesor']))
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('notifications.edit') }}">
                <span class="text-center ms-n2 me-1" style="width: 1.5rem;">
                    <i class="fas fa-bell text-primary"></i>
                </span> {{ __('Notification settings') }}
            </a>
        @endif
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ url('/profile') }}">
            <span class="text-center ms-n2 me-1" style="width: 1.5rem;">
                <i class="fas fa-user text-primary"></i>
            </span> {{ __('Profile') }}
        </a>
        <a class="dropdown-item" href="{{ url('/password') }}">
            <span class="text-center ms-n2 me-1" style="width: 1.5rem;">
                <i class="fas fa-key text-primary"></i>
            </span> {{ __('Password') }}
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="text-center ms-n2 me-1" style="width: 1.5rem;">
                <i class="fas fa-sign-out-alt"></i>
            </span> {{ __('Logout') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</li>
