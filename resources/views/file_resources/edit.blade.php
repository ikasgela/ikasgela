@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit files resource')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($file_resource, 'PUT', route('file_resources.update', $file_resource->id))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
