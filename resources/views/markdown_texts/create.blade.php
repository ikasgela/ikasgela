@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New markdown text')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('markdown_texts.store'))->open() }}

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
                'value' => 'master',
            ])
            @include('components.label-text', [
                'label' => __('File'),
                'name' => 'archivo',
                'value' => 'README.md',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
