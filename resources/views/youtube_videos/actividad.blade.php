@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: YouTube videos')])

    @include('partials.cabecera_actividad')

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($youtube_videos) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th class="text-center">{{ __('Show title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th class="text-center">{{ __('Show description') }}</th>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($youtube_videos as $youtube_video)
                    <tr>
                        <td>{{ $youtube_video->id }}</td>
                        <td>{{ $youtube_video->titulo }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'youtube_video',
                                'field' => 'titulo_visible',
                            ])
                        </td>
                        <td>{{ $youtube_video->descripcion }}</td>
                        <td class="text-center">
                            @include('partials.toggler', [
                                'resource' => 'youtube_video',
                                'field' => 'descripcion_visible',
                            ])
                        </td>
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

            <div class="mb-4">
                <button type="submit" class="btn btn-primary me-2">{{ __('Save assigment') }}</button>
                <a class="btn btn-secondary"
                   href="{{ route('youtube_videos.create') }}">{{ __('New YouTube video') }}</a>
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
