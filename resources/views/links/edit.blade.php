@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit link')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($link, 'PUT', route('links.update', $link->id))->open() }}

            @include('components.label-text', [
                'label' => __('URL'),
                'name' => 'url',
                'placeholder' => 'https://ikasgela.com',
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
