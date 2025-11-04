@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Activities') }}
            <a class="ms-3"
               style="color:#1D6F42" {{-- https://www.schemecolor.com/microsoft-excel-logo-color.php --}}
               title="{{ __('Export to an Excel file') }}"
               href="{{ route('actividades.export') }}"><i class="bi bi-file-earmark-excel-fill"></i>
            </a>
        </h1>
        <h2 class="text-muted font-xl">{{ Auth::user()->curso_actual()?->pretty_name }}</h2>
    </div>

    <div class="d-flex justify-content-end">
        <div class="btn-toolbar" role="toolbar">
            {{ html()->form('POST', route('actividades.plantillas.filtro'))->open() }}
            {{ html()->submit(__('Clear filters'))
                    ->class(['btn btn-sm mx-1 mb-3', (session('profesor_filtro_etiquetas') == 'S' || session('profesor_filtro_actividades_etiquetas') == 'S') ? 'btn-primary' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_etiquetas', 'N') }}
            {{ html()->form()->close() }}
        </div>
    </div>

    @include('actividades.selector_unidad')

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('actividades.create') }}">{{ __('New activity template') }}</a>
    </div>

    @include('actividades.partials.tabla_actividades')

    @include('partials.paginador', ['coleccion' => $actividades])

@endsection
