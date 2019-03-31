@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Ver curso</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Nombre</h5>
            <p class="card-text">{{ $curso->nombre }}</p>
            <h5 class="card-title">Descripción</h5>
            <p class="card-text">{{ $curso->descripcion }}</p>
            <h5 class="card-title">Slug</h5>
            <p class="card-text">{{ $curso->slug }}</p>
            <p class="card-text mt-3"><a href="{{ route('cursos.index') }}" class="card-link">Volver</a></p>
        </div>
    </div>

@endsection
