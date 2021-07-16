@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Activities per day')])

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'El gráfico muestra el total de actividades enviadas desde la fecha de inicio del curso.'
    ])

    @include('partials.grafico_enviadas')

@endsection
