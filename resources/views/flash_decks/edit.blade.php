@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit deck')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($flash_deck, 'PUT', route('flash_decks.update', $flash_deck->id))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-check', [
                'label' => __('Template'),
                'name' => 'plantilla',
            ])
            @include('components.label-check', [
                'label' => trans_choice("decks.completed", 1),
                'name' => 'completado',
            ])
            @include('components.label-text', [
                'label' => __('Position'),
                'name' => 'posicion',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
