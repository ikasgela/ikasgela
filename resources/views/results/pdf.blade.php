@extends('layouts.pdf')

@section('content')

    <h1 style="margin-top:0">{{ __('Results') }}</h1>

    <table class="tabla-datos border-dark">
        <tr>
            <th style="width:4cm;">{{ __('Course') }}</th>
            <td>{{ $curso->pretty_name }}</td>
        </tr>
        @if(isset($milestone))
            <tr>
                <th>{{ __('Milestone') }}</th>
                <td>{{ $milestone->name }}</td>
            </tr>
        @endif
        <tr>
            <th>{{ __('Name') }}</th>
            <td>{{ $user->full_name }}</td>
        </tr>
        <tr>
            <th>{{ __('Date') }}</th>
            <td>{{ now()->isoFormat('L LT') }}</td>
        </tr>
    </table>

    @include('results.pdf.evaluacion_continua')

    @include('results.partials.criterios_calificacion')

    @include('results.pdf.desarrollo_competencias')

    @include('results.pdf.actividades_completadas')

@endsection
