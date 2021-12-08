@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Activities per day')])

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => trans('tutorial.grafico')
    ])

    @include('partials.grafico_enviadas')

@endsection
