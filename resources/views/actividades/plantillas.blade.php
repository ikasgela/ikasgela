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

    @include('actividades.selector_unidad')

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('actividades.create') }}">{{ __('New activity template') }}</a>
    </div>

    @include('actividades.partials.tabla_actividades')

    @include('partials.paginador', ['coleccion' => $actividades])

@endsection
