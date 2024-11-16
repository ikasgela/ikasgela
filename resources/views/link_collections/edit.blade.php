@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit link collection')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($link_collection, 'PUT', route('link_collections.update', $link_collection->id))->open() }}

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
