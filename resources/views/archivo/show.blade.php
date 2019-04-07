@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Actividad'])

    @include('alumnos.partials.tarea')

    @include('partials.backbutton')

    <div>&nbsp;</div>
@endsection
