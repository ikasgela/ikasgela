@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Control panel') }}</h1>
        <div class="form-inline">
            <div class="btn-toolbar" role="toolbar">

                {!! Form::open(['route' => ['profesor.tareas.filtro', $user->id], 'method' => 'POST']) !!}
                {!! Form::button(__('View all'), ['type' => 'submit',
                    'class' => session('profesor_filtro_alumnos') == 'A' ? 'btn btn-sm mx-1 btn-secondary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos','A') !!}
                {!! Form::close() !!}

                {!! Form::open(['route' => ['profesor.tareas.filtro', $user->id], 'method' => 'POST']) !!}
                {!! Form::button(__('Pending review'), ['type' => 'submit',
                    'class' => session('profesor_filtro_alumnos') == 'R' ? 'btn btn-sm mx-1 btn-secondary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos','R') !!}
                {!! Form::close() !!}

                {!! Form::open(['route' => ['profesor.tareas.filtro', $user->id], 'method' => 'POST']) !!}
                {!! Form::button(__('Exams'), ['type' => 'submit',
                    'class' => session('profesor_filtro_alumnos') == 'E' ? 'btn btn-sm mx-1 btn-secondary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos','E') !!}
                {!! Form::close() !!}
            </div>
        </div>
        <h2 class="text-muted font-xl"></h2>
    </div>

    @include('profesor.partials.selector_usuario')

    @include('profesor.partials.asignadas')

    @include('profesor.partials.disponibles')

@endsection
