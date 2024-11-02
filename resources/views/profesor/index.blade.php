@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Control panel') }}
            @include('partials.boton_recargar')
        </h1>
        <h2 class="text-muted">{{ Auth::user()->curso_actual()?->pretty_name }}</h2>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <div class="btn-toolbar" role="toolbar">

            {{ html()->form('POST', route('profesor.index.filtro'))->open() }}
            {{ html()->submit(__('Alphabetic order'))
                    ->class(['btn btn-sm mx-1', session('profesor_filtro_alumnos') == 'A' ? 'btn-secondary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_alumnos', 'A') }}
            {{ html()->form()->close() }}

            {{ html()->form('POST', route('profesor.index.filtro'))->open() }}
            {{ html()->submit(__('Pending review'))
                    ->class(['btn btn-sm mx-1', session('profesor_filtro_alumnos') == 'R' ? 'btn-secondary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_alumnos', 'R') }}
            {{ html()->form()->close() }}

            {{ html()->form('POST', route('profesor.index.filtro'))->open() }}
            {{ html()->submit(__('Progress'))
                    ->class(['btn btn-sm mx-1', session('profesor_filtro_alumnos') == 'P' ? 'btn-secondary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_alumnos', 'P') }}
            {{ html()->form()->close() }}

            <span class="mx-1"></span>

            {{ html()->form('POST', route('profesor.index.filtro'))->open() }}
            {{ html()->submit(session('profesor_filtro_alumnos_bloqueados') == 'B' ? __('Hide blocked') : __('Show blocked'))
                    ->class(['btn btn-sm mx-1', session('profesor_filtro_alumnos_bloqueados') == 'B' ? 'btn-primary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_alumnos_bloqueados', 'B') }}
            {{ html()->form()->close() }}

            {{ html()->form('POST', route('profesor.index.filtro'))->open() }}
            {{ html()->submit(__('Clear filters'))
                    ->class(['btn btn-sm mx-1', (session('profesor_filtro_etiquetas') == 'S' || session('profesor_filtro_actividades_etiquetas') == 'S') ? 'btn-primary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_etiquetas', 'N') }}
            {{ html()->form()->close() }}

            <span class="mx-1"></span>

            {{ html()->form('POST', route('profesor.index.filtro'))->open() }}
            {{ html()->submit(__('Exams'))
                    ->class(['btn btn-sm mx-1', session('profesor_filtro_actividades_examen') == 'E' ? 'btn-primary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_actividades_examen', 'E') }}
            {{ html()->form()->close() }}
        </div>
    </div>

    @include('profesor.partials.tabla_usuarios')

    @include('profesor.partials.disponibles_grupo')
@endsection
