@extends('layouts.app')

@include('partials.prismjs')

@section('content')

    @include('partials.titular', ['titular' => 'Actividad'])

    @include('alumnos.partials.tarea')

    @include('partials.backbutton')

@endsection
