<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}{{ subdominio() != 'ikasgela' ? ' | '. subdominio() :  '' }}</title>
    <style>
        body {
            font-size: 8pt
        }

        table {
            border-spacing: 0;
            width: 100%
        }

        td, th {
            padding: 0
        }

        br {
            page-break-before: always
        }

        h2 {
            color: #2f353a
        }

        .border-dark {
            border: 1px solid #2f353a
        }

        .bg-success {
            background-color: #4dbd74
        }

        .bg-warning {
            background-color: #ffc107
        }

        .bg-secondary {
            background-color: #c8ced3
        }

        .bg-light {
            background-color: #f0f3f5
        }

        .bg-dark {
            background-color: #2f353a
        }

        .text-center {
            text-align: center
        }

        .text-left {
            text-align: left
        }

        .text-right {
            text-align: right
        }

        .text-dark {
            color: #2f353a
        }

        .text-white {
            color: #fff
        }

        .p-0 {
            padding: 0
        }

        .tabla-marcador-contenedor {
            border-spacing: 1em
        }

        .tabla-marcador td, .tabla-marcador th {
            border-left: 1px solid #2f353a;
            border-right: 1px solid #2f353a;
            padding: 1em
        }

        .tabla-marcador th {
            border: 1px solid #2f353a;
            font-size: 80%;
        }

        .tabla-marcador td {
            border-bottom: 1px solid #2f353a;
            font-size: 125%;
        }

        .tabla-datos td, .tabla-datos th {
            padding: .5em 1em
        }

        .tabla-datos th {
            background-color: #2f353a;
            color: #fff
        }

        .tabla-datos tr:nth-child(2n) {
            background-color: #f0f3f5;
            color: #2f353a
        }

        .tabla-datos tr:nth-child(odd) {
            background-color: #fff;
            color: #2f353a
        }

        #header {

        }

        #footer {
            width: 100%;
            text-align: center;
            position: fixed;
            bottom: 0;
        }
    </style>
</head>
<body>
<div id="header">
    <img style="height:1cm;" src="{{ public_path('/img/logo.png') }}"/>
</div>
@yield('content')
<div id="footer">
    <span>© {{ date('Y') }} {{ config('app.company') }}. @lang('All rights reserved.')</span>
</div>
</body>
</html>
