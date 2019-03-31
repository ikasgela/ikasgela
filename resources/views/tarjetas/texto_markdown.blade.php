@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Tarjeta: Texto en formato Markdown</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">Apuntes</div>
                <div class="card-body">
                    @markdown($readme)
                </div>
                <div class="card-footer d-flex flex-row justify-content-between">
                    <div>10 puntos</div>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>
@endsection