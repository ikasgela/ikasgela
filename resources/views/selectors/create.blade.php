@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New selector')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('selectors.store'))->open() }}

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
