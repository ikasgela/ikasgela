@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Actividad'])

    @include('tareas.show')

    @include('partials.backbutton')

@endsection
