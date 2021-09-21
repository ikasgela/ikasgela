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
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @if(config('app.env') == 'production')
        @yield('recaptcha')
    @endif
</head>
<body class="c-app">
<div class="c-wrapper" id="app">
    <div class="c-body flex-row align-items-center">
        <main class="c-main">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    @yield('auth')
                </div>
                <div class="mt-3 mb-5 text-center">
                    <a class="text-secondary" href="{{ url('/') }}">{{ __('Return to the homepage') }}</a>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
