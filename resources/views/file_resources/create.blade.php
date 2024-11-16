@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New files resource')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('file_resources.store'))->open() }}

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
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
