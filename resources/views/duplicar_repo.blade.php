@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Fork</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <p>{!! $resultado !!}</p>
            <p><a class="btn btn-primary" href="{{ url('/tarjeta_gitlab_intellij') }}">Ejercicio</a></p>

        </div>
    </div>
@endsection