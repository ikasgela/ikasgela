<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    @include('layouts.icons')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('recaptcha')
</head>
<body>
<div id="app" class="app flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            @yield('auth')
        </div>
        <div class="m-3 text-center">
            <a class="text-secondary" href="{{ url('/') }}">{{ __('Return to the homepage') }}</a>
        </div>
    </div>
</div>
</body>
</html>
