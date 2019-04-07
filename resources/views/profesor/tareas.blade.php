@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Control panel')])

    @include('profesor.partials.selector_usuario')

    @include('profesor.partials.asignadas')

    @include('profesor.partials.disponibles')

@endsection
