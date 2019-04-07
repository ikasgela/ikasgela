@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Revisar tarea'])

    @include('profesor.partials.tarea')

@endsection
