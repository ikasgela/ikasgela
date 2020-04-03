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
    <link href="{{ asset('css/pdf.css') }}" rel="stylesheet">
</head>
<body>
<header>
    <img style="height:1cm;" src="{{ public_path('/svg/logo.svg') }}"/>
</header>
@yield('content')
<footer>
    <span>© {{ date('Y') }} {{ config('app.company') }}. @lang('All rights reserved.')</span>
</footer>
</body>
</html>
