@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New image upload')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('file_uploads.store'))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-text', [
                'label' => __('Maximum'),
                'name' => 'max_files',
                'value' => 1,
            ])
            @include('components.label-check', [
                'label' => __('Template'),
                'name' => 'plantilla',
                'checked' => true,
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
