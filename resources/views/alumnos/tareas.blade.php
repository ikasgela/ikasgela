@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Asignar actividades'])

    @include('alumnos.tareas.selector_usuario')

    @include('alumnos.tareas.asignadas')

    @include('alumnos.tareas.disponibles')

@endsection
