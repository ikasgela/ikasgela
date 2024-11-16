@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New item')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('items.store'))->open() }}

            @include('components.label-value', [
                'label' => __('Question'),
                'name' => 'pregunta_id',
                'value' => $pregunta->titulo,
                'hidden' => $pregunta->id,
            ])
            @include('components.label-text', [
                'label' => __('Text'),
                'name' => 'texto',
            ])
            @include('components.label-check', [
                'label' => __('Correct'),
                'name' => 'correcto',
            ])
            @include('components.label-check', [
                'label' => __('Selected'),
                'name' => 'seleccionado',
            ])
            @include('components.label-text', [
                'label' => __('Feedback'),
                'name' => 'feedback',
            ])
            @include('components.label-text', [
                'label' => __('Order'),
                'name' => 'orden',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
