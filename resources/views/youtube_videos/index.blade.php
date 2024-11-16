@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: YouTube videos')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'youtube_videos.index.filtro'])
    @endif

    <div class="mb-3">
        <a class="btn btn-primary"
           href="{{ route('youtube_videos.create') }}">{{ __('New YouTube video') }}</a>
    </div>

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
                        <a title="{{ __('Preview') }}" target="_blank"
                           href="{{ $youtube_video->codigo }}">{{ $youtube_video->codigo }}</a>
                    </td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_mostrar', ['ruta' => 'youtube_videos', 'recurso' => $youtube_video])
                            @include('partials.boton_editar', ['ruta' => 'youtube_videos', 'recurso' => $youtube_video])
                            @include('partials.boton_duplicar', ['ruta' => 'youtube_videos.duplicar', 'id' => $youtube_video->id])
                            {{ html()->form('DELETE', route('youtube_videos.destroy', $youtube_video->id))->open() }}
                            @include('partials.boton_borrar')
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
