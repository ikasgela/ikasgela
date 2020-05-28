@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Preview')])

    @include('actividades.partials.preview_siguiente')

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Vista previa de la actividad.'
    ])

    <div class="row mt-4">
        <div class="col-md-12">
            {{-- Tarjeta --}}
            <div class="card border-dark">
                <div class="card-header text-white bg-dark d-flex justify-content-between">
                    <span>{{ $actividad->unidad->curso->nombre }} » {{ $actividad->unidad->nombre }}</span>
                </div>
                <div class="card-body">
                    @include('actividades.partials.encabezado_con_etiquetas')
                    <p>{{ $actividad->descripcion }}</p>
                </div>
                @include('partials.tarjetas_actividad')
            </div>
            {{-- Fin tarjeta--}}
        </div>
    </div>
    @include('partials.backbutton')
@endsection
