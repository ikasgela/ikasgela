@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Control panel'), 'subtitulo' => $organization->name ?? '?' ])

    @include('profesor.partials.tabla_usuarios')

    @include('profesor.partials.disponibles_grupo')

@endsection
