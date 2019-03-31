@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Help') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">Vídeo</div>
                <div class="card-body">
                    <h5 class="card-title">Primeros pasos</h5>
                    <h6 class="card-subtitle text-muted mb-2">Crear una cuenta de usuario</h6>
                    <p class="card-text">Guía paso a paso que te enseñará cómo conseguir un usuario y contraseña para utilizar Ikasgela.</p>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item"
                                src="https://www.youtube.com/embed/E_fQy6TRRyM?rel=0&modestbranding=1"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>
@endsection
