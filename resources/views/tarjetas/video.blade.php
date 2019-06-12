@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Tarjeta: Vídeo</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">YouTube</div>
                <div class="card-body">
                    <h5 class="card-title">El bucle while</h5>
                    <h6 class="card-subtitle text-muted mb-2">Estructuras de control</h6>
                    <p class="card-text">Sintáxis del bucle while en Java.</p>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item"
                                src="https://www.youtube.com/embed/nafC9AUyfCk?rel=0&modestbranding=1"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                </div>
                <div class="card-footer d-flex flex-row justify-content-between">
                    <div>2 puntos</div>
                    <div>02:00</div>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
        <div class="col-md-6">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">Vimeo</div>
                <div class="card-body">
                    <h5 class="card-title">Listas - Ejercicio resuelto: Ticket</h5>
                    <h6 class="card-subtitle text-muted mb-2">Estructuras de datos II</h6>
                    <p class="card-text">Ejercicio sobre un ticket de supermercado
                        compuesto de líneas de ticket, almacenadas en un
                        <code>List<></code>.</p>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe src="https://player.vimeo.com/video/341841909" width="640" height="360" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="card-footer d-flex flex-row justify-content-between">
                    <div>2 puntos</div>
                    <div>02:00</div>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>
@endsection