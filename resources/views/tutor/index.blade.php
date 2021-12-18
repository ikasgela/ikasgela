@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Group report') }}
            @if(config('ikasgela.excel_report_enabled'))
                <a class="ml-3"
                   style="color:#1D6F42" {{-- https://www.schemecolor.com/microsoft-excel-logo-color.php --}}
                   title="{{ __('Export to an Excel file') }}"
                   href="{{ route('tutor.export') }}"><i class="fas fa-file-excel"></i>
                </a>
            @endif
        </h1>
        <div class="form-inline">
            <div class="btn-toolbar" role="toolbar">

                {!! Form::open(['route' => ['tutor.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Alphabetic order'), ['type' => 'submit',
                    'class' => session('tutor_filtro_alumnos') == 'A' ? 'btn btn-sm mx-1 btn-secondary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos','A') !!}
                {!! Form::close() !!}

                {!! Form::open(['route' => ['tutor.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Progress'), ['type' => 'submit',
                    'class' => session('tutor_filtro_alumnos') == 'P' ? 'btn btn-sm mx-1 btn-secondary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_alumnos','P') !!}
                {!! Form::close() !!}

            </div>
        </div>
        <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
    </div>

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => trans('tutorial.resultados_tutor')
    ])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['tutor.index'], 'method' => 'POST']) !!}
        @include('partials.desplegable_milestones')
        {!! Form::close() !!}
    @endif

    @include('tutor.partials.tabla_usuarios')

    <div>
        <p class="text-center text-muted font-xs">{{ now()->isoFormat('L LT') }}</p>
    </div>
@endsection
