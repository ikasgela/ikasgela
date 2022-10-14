@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: YouTube videos')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['youtube_videos.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
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
                            {!! Form::open(['route' => ['youtube_videos.duplicar', $youtube_video->id], 'method' => 'POST']) !!}
                            @include('partials.boton_duplicar')
                            {!! Form::close() !!}
                            {!! Form::open(['route' => ['youtube_videos.destroy', $youtube_video->id], 'method' => 'DELETE']) !!}
                            @include('partials.boton_borrar')
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
