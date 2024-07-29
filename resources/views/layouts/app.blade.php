<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <script src="{{ asset('/js/app.js') }}"></script>
    @yield('fancybox')
    @yield('prismjs-css')
    @stack('intellij-isforking')
</head>
<body class="d-flex flex-column h-100">

@include('layouts.navbar')

<main class="row flex-grow-1 m-0">
    @include('layouts.sidebar')
    <div class="p-4 col-12 col-sm-10">
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
