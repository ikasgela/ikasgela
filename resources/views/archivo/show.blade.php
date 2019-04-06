@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Actividad'])

    @include('alumnos.tarea')

    @include('partials.backbutton')

    <div>&nbsp;</div>
@endsection
