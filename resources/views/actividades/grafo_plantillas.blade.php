@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Activity templates') }}</h1>
        <h2 class="text-muted font-xl">{{ Auth::user()->curso_actual()?->pretty_name }}</h2>
    </div>

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-center mb-3">
        <div class="btn-toolbar" role="toolbar">
            <a class="btn btn-sm btn-outline-secondary me-2" href="{{ route('actividades.plantillas') }}">
                <i class="bi bi-table me-1"></i>{{ __('Table view') }}
            </a>
            <a class="btn btn-sm btn-primary" href="{{ route('actividades.create') }}">
                <i class="bi bi-plus-lg me-1"></i>{{ __('New activity template') }}
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            {{ html()->form('POST', route('actividades.plantillas.grafo.filtro'))->open() }}
            @include('partials.desplegable_unidades', ['nombre_variable' => 'unidad_id_disponibles'])
            {{ html()->form()->close() }}
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div id="grafo-plantillas" class="grafo-plantillas"></div>
        </div>
    </div>

@endsection

@push('scripts')
    <script id="datos-grafo-plantillas" type="application/json">{!! \Illuminate\Support\Js::encode($datos) !!}</script>
    @vite(['resources/js/grafo-plantillas/main.js'])
@endpush
