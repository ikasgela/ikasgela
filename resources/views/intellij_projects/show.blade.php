@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Ver repositorio de IntelliJ</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Repositorio</h5>
            <p class="card-text">{{ $intellij_project->repositorio }}</p>
            <p class="card-text mt-3"><a href="{{ route('intellij_projects.index') }}" class="card-link">Volver</a></p>
        </div>
    </div>

@endsection
