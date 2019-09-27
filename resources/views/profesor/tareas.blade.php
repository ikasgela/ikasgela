@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Control panel') }}</h1>
        <div class="form-inline">
            {!! Form::open(['route' => ['profesor.tareas', $user->id], 'method' => 'POST']) !!}
            {!! Form::button(__('View all'), ['type' => 'submit', 'class' => 'btn btn-link text-secondary']) !!}
            {!! Form::hidden('filtro_alumnos','A') !!}
            {!! Form::close() !!}

            {!! Form::open(['route' => ['profesor.tareas', $user->id], 'method' => 'POST']) !!}
            {!! Form::button(__('Pending review'), ['type' => 'submit', 'class' => 'btn btn-link text-secondary']) !!}
            {!! Form::hidden('filtro_alumnos','R') !!}
            {!! Form::close() !!}
        </div>
        <h2 class="text-muted font-xl"></h2>
    </div>

    @include('profesor.partials.selector_usuario')

    @include('profesor.partials.asignadas')

    @include('profesor.partials.disponibles')

@endsection
