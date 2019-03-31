@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Tarjeta: PDF</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">Apuntes</div>
                <div class="card-body">
                    <p>
                        <i class="fas fa-file-pdf mr-2" style="font-size:2em;color:#D70909"></i>
                        <a href="{{ url('/pdf/test.pdf') }}" target="_blank"> Archivo PDF</a>
                    </p>
                    <p>
                        <i class="fas fa-file-word mr-2" style="font-size:2em;color:#295699"></i>
                        <a href="{{ url('/pdf/test.pdf') }}" target="_blank"> Archivo de Microsoft Word.</a>
                    </p>
                </div>
                <div class="card-footer d-flex flex-row justify-content-between">
                    <div>10 puntos</div>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>
@endsection
