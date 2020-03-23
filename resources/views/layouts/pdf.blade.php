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
    <style>
        table {
            width: 100%;
            border: 1px solid black;
        }

        th {
            background-color: #8f9ca6;
        }
    </style>
</head>
<body class="c-app">
<div class="c-wrapper" id="app">
    <header>
        <img src="{{ public_path('/svg/logo.svg') }}"/>
    </header>
    <div class="c-body">
        <main class="c-main">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
    </div>
    @include('layouts.footer')
</div>
</body>
</html>
