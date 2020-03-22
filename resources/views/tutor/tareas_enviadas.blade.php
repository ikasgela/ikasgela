@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Activities per day') }}</h1>
        <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
    </div>

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'El gráfico muestra el total de actividades enviadas desde la fecha de inicio del curso.'
    ])

    @include('partials.grafico_enviadas')

@endsection
