@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Recursos: Vídeos en YouTube</h1>
        </div>
    </div>

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('youtube_videos.create') }}">Nuevo vídeo en YouTube</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Código</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($youtube_videos as $youtube_video)
                <tr>
                    <td class="py-3">{{ $youtube_video->titulo }}</td>
                    <td class="py-3">{{ $youtube_video->descripcion }}</td>
                    <td class="py-3">{{ $youtube_video->codigo }}</td>
                    <td>
                        <form method="POST" action="{{ route('youtube_videos.destroy', [$youtube_video->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a href="{{ route('youtube_videos.show', [$youtube_video->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                                <a href="{{ route('youtube_videos.edit', [$youtube_video->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                <button type="submit" onclick="return confirm('¿Seguro?')"
                                        class="btn btn-light btn-sm"><i class="fas fa-trash text-danger"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
