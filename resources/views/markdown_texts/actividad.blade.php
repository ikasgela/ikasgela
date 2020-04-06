@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Markdown texts')])

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

    @if(count($markdown_texts) > 0 )
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Repository') }}</th>
                    <th>{{ __('Branch') }}</th>
                    <th>{{ __('File') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($markdown_texts as $markdown_text)
                    <tr>
                        <td>{{ $markdown_text->id }}</td>
                        <td>{{ $markdown_text->titulo }}</td>
                        <td>{{ $markdown_text->descripcion }}</td>
                        <td>{{ $markdown_text->repositorio }}</td>
                        <td>{{ $markdown_text->rama }}</td>
                        <td>
                            <a target="_blank"
                               href="https://gitlab.ikasgela.{{ config('app.debug') ? 'test' : 'com' }}/{{ $markdown_text->repositorio }}/blob/{{ isset($markdown_text->rama) ? $markdown_text->rama : 'master' }}/{{ $markdown_text->archivo }}">{{ $markdown_text->archivo }}</a>
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('markdown_texts.desasociar', ['actividad' => $actividad->id, 'markdown_text' => $markdown_text->id]) }}">
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
        <form method="POST" action="{{ route('markdown_texts.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Repository') }}</th>
                        <th>{{ __('Branch') }}</th>
                        <th>{{ __('File') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $markdown_text)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $markdown_text->id }}"></td>
                            <td>{{ $markdown_text->id }}</td>
                            <td>{{ $markdown_text->titulo }}</td>
                            <td>{{ $markdown_text->descripcion }}</td>
                            <td>{{ $markdown_text->repositorio }}</td>
                            <td>{{ $markdown_text->rama }}</td>
                            <td>
                                <a target="_blank"
                                   href="https://gitlab.ikasgela.{{ config('app.debug') ? 'test' : 'com' }}/{{ $markdown_text->repositorio }}/blob/{{ isset($markdown_text->rama) ? $markdown_text->rama : 'master' }}/{{ $markdown_text->archivo }}">{{ $markdown_text->archivo }}</a>
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
