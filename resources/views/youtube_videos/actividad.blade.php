@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Recursos: Youtube</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</div>
                <div class="card-body pb-1">
                    <h2>{{ $actividad->nombre }}</h2>
                    <p>{{ $actividad->descripcion }}</p>
                </div>
            </div>
            {{-- Fin tarjeta--}}
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md">
            <h2>Recursos asignados</h2>
        </div>
    </div>

    <?php if (count($youtube_videos) > 0 ) { ?>
    <div class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>Titulo</th>
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
                        <form method="POST"
                              action="{{ route('youtube_videos.desasociar', ['actividad' => $actividad->id, '$youtube_video'=>$youtube_video->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
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
    <?php } else { ?>
    <div class="row">
        <div class="col-md">
            <p>No hay elementos.</p>
        </div>
    </div>
    <?php } ?>

    <div class="row mb-3">
        <div class="col-md">
            <h2>Recursos disponibles</h2>
        </div>
    </div>

    <?php if (count($disponibles) > 0 ) { ?>
    <form method="POST" action="{{ route('youtube_videos.asociar', ['actividad' => $actividad->id]) }}">
        @csrf

        <div class="table-responsive">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th>Seleccionar</th>
                    <th>Título</th>
                </tr>
                </thead>
                <tbody>
                @foreach($disponibles as $youtube_video)
                    <tr>
                        <td class="py-3"><input type="checkbox" name="seleccionadas[]" value="{{ $youtube_video->id }}">
                        </td>
                        <td class="py-3">{{ $youtube_video->titulo }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @include('layouts.errors')

        <div>
            <button type="submit" class="btn btn-primary mb-4">Guardar asignación</button>
        </div>

    </form>
    <?php } else { ?>
    <div class="row">
        <div class="col-md">
            <p>No hay elementos.</p>
        </div>
    </div>
    <?php } ?>

    <div>
        @include('partials.backbutton')
    </div>
@endsection
