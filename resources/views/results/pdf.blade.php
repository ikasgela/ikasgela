@extends('layouts.pdf')

@section('content')

    <h1>{{ __('Results') }}</h1>

    <table class="tabla-datos border-dark">
        <tr>
            <th style="width:4cm;">{{ __('Course') }}</th>
            <td>{{ $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre  ?? '' }}</td>
        </tr>
        <tr>
            <th>{{ __('Name') }}</th>
            <td>{{ $user->name }} {{ $user->surname }}</td>
        </tr>
        <tr>
            <th>{{ __('Date') }}</th>
            <td>{{ now()->isoFormat('LLLL') }}</td>
        </tr>
    </table>

    @include('results.pdf.evaluacion_continua')

    @include('results.partials.criterios_calificacion')

    @include('results.pdf.desarrollo_competencias')

    @include('results.pdf.actividades_completadas')

@endsection
