<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ config('app.name', 'Laravel') }}
        {{ subdominio() != 'ikasgela' ? ' | '. subdominio() :  '' }}
    </title>
    @include('layouts.icons')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('fancybox')
    @yield('prismjs-css')
    @stack('intellij-isforking')
</head>

<body class="c-app">
@include('layouts.sidebar')
<div class="c-wrapper">
    @include('layouts.header')
    <div class="c-body">
        <main class="c-main">
            <div class="container-fluid">
                @yield('content')
                @auth
                    @if(Auth::user()->isVerified())
                        @include('partials.tutorial', [
                            'color' => 'c-callout-danger',
                            'texto' => 'Puedes desactivar el tutorial en tu perfil de usuario.'
                        ])
                    @endif
                @endauth
            </div>
        </main>
    </div>
    @include('layouts.footer')
</div>
@yield('tinymce')
@yield('prismjs-scripts')
</body>

{{--
<body class="c-app c-header-fixed  c-footer-fixed">
<div id="app">
    <div class="c-body">
        <main class="c-main">
        </main>
    </div>
</div>
</body>
--}}
</html>
