@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Tarjeta: Reloj</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-body">

                    <p>Tiempo restante: <span data-countdown="2020-01-01 00:00:00"></span></p>

                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>

@endsection
