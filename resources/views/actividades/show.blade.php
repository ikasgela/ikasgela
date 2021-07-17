@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => $actividad->nombre])

    @include('actividades.partials.tabla_recursos')

    @include('partials.backbutton')

@endsection
