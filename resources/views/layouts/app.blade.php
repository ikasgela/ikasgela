<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ config('app.name', 'Laravel') }}
        {{ Request::route('organization') ? ' | '. Request::route('organization') :  '' }}
    </title>
    @include('layouts.icons')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('tinymce')
</head>
<body class="app header-fixed sidebar-fixed sidebar-lg-show footer-fixed">
<div id="app">
    @include('layouts.header')
    <div class="app-body">
        @include('layouts.sidebar')
        <main class="main">
            <div class="container-fluid py-4">
                @yield('content')
                @if(Auth::user()->isVerified() && session('tutorial'))
                    <div class="callout callout-danger b-t-1 b-r-1 b-b-1">
                        <small class="text-muted">{{ __('Tutorial') }}</small>
                        <p>Puedes desactivar el tutorial en tu perfil de usuario.</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
    @include('layouts.footer')
</div>
</body>
</html>
