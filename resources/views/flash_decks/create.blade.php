@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New deck')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('flash_decks.store'))->open() }}

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
                'checked' => true,
            ])
            @include('components.label-check', [
                'label' => trans_choice("decks.completed", 1),
                'name' => 'completado',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
