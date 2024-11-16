@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit image upload')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($file_upload, 'PUT', route('file_uploads.update', $file_upload->id))->open() }}

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
            ])
            @include('components.label-check', [
                'label' => __('Template'),
                'name' => 'plantilla',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
