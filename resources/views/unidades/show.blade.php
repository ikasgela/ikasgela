@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Ver unidad</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Curso</h5>
            <p class="card-text">{{ $unidad->curso->nombre }}</p>
            <h5 class="card-title">Nombre</h5>
            <p class="card-text">{{ $unidad->nombre }}</p>
            <h5 class="card-title">Descripción</h5>
            <p class="card-text">{{ $unidad->descripcion }}</p>
            <h5 class="card-title">Slug</h5>
            <p class="card-text">{{ $unidad->slug }}</p>
            <p class="card-text mt-3"><a href="{{ route('unidades.index') }}" class="card-link">Volver</a></p>
        </div>
    </div>

@endsection
