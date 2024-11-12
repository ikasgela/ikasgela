@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Group report') }}
            @if(config('ikasgela.excel_report_enabled'))
                <a class="ms-3"
                   style="color:#1D6F42" {{-- https://www.schemecolor.com/microsoft-excel-logo-color.php --}}
                   title="{{ __('Export to an Excel file') }}"
                   href="{{ route('tutor.export') }}"><i class="fas fa-file-excel"></i>
                </a>
            @endif
        </h1>
        <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <div class="btn-toolbar" role="toolbar">
            {{ html()->form('POST', route('tutor.index.filtro'))->open() }}
            {{ html()->submit(__('Alphabetic order'))
                    ->class(['btn btn-sm mx-1', session('tutor_filtro_alumnos') == 'A' ? 'btn-secondary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_alumnos', 'A') }}
            {{ html()->form()->close() }}

            {{ html()->form('POST', route('tutor.index.filtro'))->open() }}
            {{ html()->submit(__('Progress'))
                    ->class(['btn btn-sm mx-1', session('tutor_filtro_alumnos') == 'P' ? 'btn-secondary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_alumnos', 'P') }}
            {{ html()->form()->close() }}

            {{ html()->form('POST', route('tutor.index.filtro'))->open() }}
            {{ html()->submit(session('tutor_informe_anonimo') == 'A' ? __('Hide names') : __('Show names'))
                    ->class(['btn btn-sm mx-1', session('tutor_informe_anonimo') == 'A' ? 'btn-primary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('informe_anonimo', 'A') }}
            {{ html()->form()->close() }}

            {{ html()->form('POST', route('cursos.limpiar_cache', [$curso->id]))->open() }}
            {{ html()->submit(__('Reload results'))
                    ->class('btn btn-sm btn-outline-secondary') }}
            {{ html()->form()->close() }}
        </div>
    </div>

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.resultados_tutor')
    ])

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        <div class="mb-3">
            {{ html()->form('POST', route('tutor.index'))->open() }}
            @include('partials.desplegable_usuarios')
            {{ html()->form()->close() }}
        </div>
    @endif

    @if(Auth::user()->hasAnyRole(['admin']))
        <div class="mb-3">
            {{ html()->form('POST', route('tutor.index'))->open() }}
            @include('partials.desplegable_milestones')
            {{ html()->form()->close() }}
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            @include('tutor.partials.criterios_ajuste_nota')
        </div>
    </div>

    @include('tutor.partials.tabla_usuarios')

    <div>
        <p class="text-center text-secondary small">{{ now()->isoFormat('L LT') }}</p>
    </div>
@endsection
