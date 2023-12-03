@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Control panel') }}
            @include('partials.boton_recargar')
        </h1>
        <div class="form-inline">
            <div class="btn-toolbar" role="toolbar">

                {!! Form::open(['route' => ['profesor.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Alphabetic order'), ['type' => 'submit',
                    'class' => session('profesor_filtro_alumnos') == 'A' ? 'btn btn-sm mx-1 btn-secondary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos','A') !!}
                {!! Form::close() !!}

                {!! Form::open(['route' => ['profesor.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Pending review'), ['type' => 'submit',
                    'class' => session('profesor_filtro_alumnos') == 'R' ? 'btn btn-sm mx-1 btn-secondary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos','R') !!}
                {!! Form::close() !!}

                {!! Form::open(['route' => ['profesor.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Progress'), ['type' => 'submit',
                    'class' => session('profesor_filtro_alumnos') == 'P' ? 'btn btn-sm mx-1 btn-secondary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos','P') !!}
                {!! Form::close() !!}

                <span class="mx-1"></span>

                {!! Form::open(['route' => ['profesor.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(session('profesor_filtro_alumnos_bloqueados') == 'B' ? __('Hide blocked') : __('Show blocked'), ['type' => 'submit',
                    'class' => session('profesor_filtro_alumnos_bloqueados') == 'B' ? 'btn btn-sm mx-1 btn-primary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos_bloqueados','B') !!}
                {!! Form::close() !!}

                {!! Form::open(['route' => ['profesor.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Clear filters'), ['type' => 'submit',
                    'class' => (session('profesor_filtro_etiquetas') == 'S' || session('profesor_filtro_actividades_etiquetas') == 'S')
                                    ? 'btn btn-sm mx-1 btn-primary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_etiquetas','N') !!}
                {!! Form::close() !!}

                <span class="mx-1"></span>

                {!! Form::open(['route' => ['profesor.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Exams'), ['type' => 'submit',
                    'class' => session('profesor_filtro_actividades_examen') == 'E' ? 'btn btn-sm mx-1 btn-primary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_actividades_examen','E') !!}
                {!! Form::close() !!}
            </div>
        </div>
        <h2 class="text-muted font-xl">{{ Auth::user()->curso_actual()?->pretty_name }}</h2>
    </div>

    @include('profesor.partials.tabla_usuarios')

    @include('profesor.partials.disponibles_grupo')
@endsection
