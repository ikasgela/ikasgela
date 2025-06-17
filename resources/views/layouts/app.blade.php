<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ config('app.debug') ? '#ffc107' : '#3490dc' }}">
    <title>
        {{ config('app.name', 'Laravel') }}
        {{ subdominio() != 'ikasgela' ? ' | '. subdominio() :  '' }}
    </title>
    @if(config('app.env') == 'production')
        @include('layouts.partials.favicons')
    @else
        @include('layouts.partials.favicons_debug')
    @endif
    <link href="{{ asset('/css/nunito.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet" type="text/css">
    <livewire:modals/>
    <livewire:scripts/>
    <script src="{{ asset('/js/app.js') }}"></script>
    @yield('fancybox')
    @yield('prismjs-css')
</head>
<body class="d-flex flex-column h-100">

@include('layouts.navbar')

<nav class="navbar navbar-expand-sm p-0">
    <div class="p-3 text-bg-dark col-12 d-sm-none">
        <button class="navbar-toggler border-0 text-bg-dark" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#sidebar" aria-controls="sidebar"
                aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <i class="bi bi-list"></i>
        </button>
    </div>
</nav>
<main class="row flex-grow-1 m-0">
    <div class="collapse py-0 pt-0 pb-3 p-sm-3 text-bg-dark col-12 d-xs d-sm-block col-sm-3 col-xl-2" id="sidebar">
        @include('layouts.sidebar')
    </div>
    <div class="p-3 p-sm-4 col-12 col-sm-9 col-xl-10">
        @yield('content')
        @auth
            @if(Auth::user()->isVerified())
                @include('partials.tutorial', [
                    'color' => 'danger',
                    'texto' => trans('tutorial.ocultable')
                ])
            @endif
        @endauth
    </div>
</main>

@include('layouts.footer')

@yield('tinymce')
@yield('prismjs-scripts')
@yield('reload_position')
</body>
</html>
