@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Results') }}</h1>
        </div>
    </div>

    <h2>Programación</h2>

    <div class="row">
        <div class="col-1">
            <h3>CE1</h3>
        </div>
        <div class="col-11">
            <p class="text-muted small">Especificar, diseñar e implementar algoritmos en un lenguaje de programación,
                utilizando métodos eficientes, sistemáticos y organizados de resolución de problemas.
            </p>

            <div class="progress" style="height: 24px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: 78%;" aria-valuenow="78"
                     aria-valuemin="0" aria-valuemax="100">78%
                </div>
            </div>

            <div class="text-muted small text-right">3460/5000</div>

        </div>
    </div>

    <div class="row">
        <div class="col-1">
            <h3>CE2</h3>
        </div>
        <div class="col-11">
            <p class="text-muted small">Escribir correctamente, compilar y ejecutar programas en un lenguaje de
                alto nivel.
            </p>

            <div class="progress" style="height: 24px;">
                <div class="progress-bar bg-danger" role="progressbar" style="width: 25%;" aria-valuenow="25"
                     aria-valuemin="0" aria-valuemax="100">25%
                </div>
            </div>

            <div class="text-muted small text-right">3460/5000</div>

        </div>
    </div>

    <div class="row">
        <div class="col-1">
            <h3>CE3</h3>
        </div>
        <div class="col-11">
            <p class="text-muted small">Conocer y dominar estructuras básicas fundamentales utilizadas en la
                programación, tanto estructuras de datos como estructuras de control del flujo del programa.
            </p>

            <div class="progress" style="height: 24px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0"
                     aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div class="text-muted small text-right">0/5000</div>

        </div>
    </div>

@endsection
