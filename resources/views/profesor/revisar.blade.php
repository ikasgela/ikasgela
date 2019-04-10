@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Assigment review')])

    @include('profesor.partials.tarea')

@endsection
