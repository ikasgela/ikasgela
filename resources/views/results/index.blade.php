@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Results') }}</h1>
        </div>
    </div>

    {{-- Tarjeta --}}
    <div class="card">
        <div class="card-header">Programación</div>
        <div class="card-body">
            <h5 class="card-title">CE1</h5>
            <p class="ml-5">Especificar, diseñar e implementar algoritmos en un
                lenguaje de
                programación,
                utilizando métodos eficientes, sistemáticos y organizados de resolución de problemas.</p>
            <div class="ml-5 progress" style="height: 24px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: 78%;" aria-valuenow="78"
                     aria-valuemin="0" aria-valuemax="100">78%
                </div>
            </div>
            <div class="text-muted small text-right">3460/5000</div>
        </div>
        <hr class="mt-0 mb-2">
        <div class="card-body">
            <h5 class="card-title">CE2</h5>
            <p class="ml-5">Escribir correctamente, compilar y ejecutar programas en un lenguaje de
                alto nivel.</p>
            <div class="ml-5 progress" style="height: 24px;">
                <div class="progress-bar bg-warning text-dark" role="progressbar" style="width: 45%;" aria-valuenow="45"
                     aria-valuemin="0" aria-valuemax="100">45%
                </div>
            </div>
            <div class="text-muted small text-right">560/2000</div>
        </div>
        <hr class="mt-0 mb-2">
        <div class="card-body">
            <h5 class="card-title">CE3</h5>
            <p class="ml-5">Conocer y dominar estructuras básicas fundamentales utilizadas en la
                programación, tanto estructuras de datos como estructuras de control del flujo del programa.</p>
            <div class="ml-5 progress" style="height: 24px;">
                <div class="progress-bar bg-light" role="progressbar" style="width: 0%;" aria-valuenow="0"
                     aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <div class="text-muted small text-right">0/500</div>
        </div>
    </div>
    {{-- Fin tarjeta--}}

@endsection
