@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit markdown text')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($markdown_text, 'PUT', route('markdown_texts.update', $markdown_text->id))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-text', [
                'label' => __('Repository'),
                'name' => 'repositorio',
            ])
            @include('components.label-text', [
                'label' => __('Branch'),
                'name' => 'rama',
            ])
            @include('components.label-text', [
                'label' => __('File'),
                'name' => 'archivo',
            ])

            @if(isset($repositorio['web_url']))
                @include('components.label-link', [
                    'label' => __('URL'),
                    'link' => $repositorio['web_url'],
                    'value' => $repositorio['web_url'],
                ])
            @endif

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
