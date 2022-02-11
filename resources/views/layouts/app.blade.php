<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
    <link href="{{ asset('css/nunito.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('fancybox')
    @yield('prismjs-css')
    @stack('intellij-isforking')
</head>
<body class="c-app">
@include('layouts.sidebar')
<div class="c-wrapper" id="app">
    @include('layouts.header')
    <div class="c-body">
        <main class="c-main">
            <div class="container-fluid">
                @yield('content')
                @auth
                    @if(Auth::user()->isVerified())
                        @include('partials.tutorial', [
                            'color' => 'c-callout-danger',
                            'texto' => trans('tutorial.ocultable')
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
</html>
