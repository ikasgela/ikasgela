@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Markdown texts')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('markdown_texts.create') }}">{{ __('New markdown text') }}</a>
    </div>

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
                    <td>{{ $markdown_text->archivo }}</td>
                    <td class="text-nowrap">
                        {!! Form::open(['route' => ['markdown_texts.destroy', $markdown_text->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('markdown_texts.edit', [$markdown_text->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                            @include('partials.boton_borrar')
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
