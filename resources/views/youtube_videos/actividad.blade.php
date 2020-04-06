@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: YouTube videos')])

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

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($youtube_videos) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($youtube_videos as $youtube_video)
                    <tr>
                        <td>{{ $youtube_video->id }}</td>
                        <td>{{ $youtube_video->titulo }}</td>
                        <td>{{ $youtube_video->descripcion }}</td>
                        <td>
                            <a target="_blank"
                               href="https://youtu.be/{{ $youtube_video->codigo }}">{{ $youtube_video->codigo }}</a>
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('youtube_videos.desasociar', ['actividad' => $actividad->id, 'youtube_video' => $youtube_video->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    @include('partials.boton_borrar')
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    @include('partials.subtitulo', ['subtitulo' => __('Available resources')])

    @if(count($disponibles) > 0)
        <form method="POST" action="{{ route('youtube_videos.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Code') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $youtube_video)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $youtube_video->id }}"></td>
                            <td>{{ $youtube_video->id }}</td>
                            <td>{{ $youtube_video->titulo }}</td>
                            <td>{{ $youtube_video->descripcion }}</td>
                            <td>
                                <a target="_blank"
                                   href="https://youtu.be/{{ $youtube_video->codigo }}">{{ $youtube_video->codigo }}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.paginador', ['coleccion' => $disponibles])

            @include('layouts.errors')

            <div>
                <button type="submit" class="btn btn-primary mb-4">{{ __('Save assigment') }}</button>
            </div>

        </form>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    <div>
        @include('partials.backbutton')
    </div>
@endsection
