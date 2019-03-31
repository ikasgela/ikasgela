@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Ver vídeo en YouTube</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Título</h5>
            <p class="card-text">{{ $youtube_video->titulo }}</p>
            <h5 class="card-title">Descripción</h5>
            <p class="card-text">{{ $youtube_video->descripcion }}</p>
            <h5 class="card-title">Código</h5>
            <p class="card-text">{{ $youtube_video->codigo }}</p>
            <p class="card-text mt-3"><a href="{{ route('youtube_videos.index') }}" class="card-link">Volver</a></p>
        </div>
    </div>

@endsection
