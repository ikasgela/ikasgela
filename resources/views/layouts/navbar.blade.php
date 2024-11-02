@php($debug_text_color = config('app.debug') ? 'dark' : 'light')
@php($debug_navbar_color = config('app.debug') ? 'navbar-light bg-warning' : 'navbar-dark bg-primary')

<nav class="navbar navbar-expand-md {{ $debug_navbar_color }} shadow-sm">
    <a class="navbar-brand text-{{ $debug_text_color }} m-0 text-start ps-3 text-md-center ps-md-0"
       style="width: 15rem;"
       href="{{ route('portada') }}">
        @include('partials.logos')
    </a>
    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
        <i class="bi bi-list text-{{ $debug_text_color }}"></i>
    </button>
    <div class="collapse navbar-collapse mx-3" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto"></ul>
        <ul class="navbar-nav ms-auto">
            @include('layouts.navbar.language-selector')
            @include('layouts.navbar.navbar-separator')
            @include('layouts.navbar.theme-selector')
            @auth
                @if(Auth::user()->isImpersonated())
                    @include('layouts.navbar.navbar-separator')
                    @include('layouts.navbar.user-impersonated')
                @endif
            @endauth
            @auth
                @include('layouts.navbar.navbar-separator')
                @include('layouts.navbar.user-dropdown')
            @endauth
        </ul>
    </div>
</nav>
