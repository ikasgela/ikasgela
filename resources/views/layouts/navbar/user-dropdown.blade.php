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
        <a class="dropdown-item" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</li>
