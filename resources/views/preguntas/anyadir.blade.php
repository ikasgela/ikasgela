@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New question')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('preguntas.store'))->open() }}

            @include('components.label-value', [
                'label' => __('Questionnaire'),
                'name' => 'cuestionario_id',
                'value' => $cuestionario->titulo,
                'hidden' => $cuestionario->id,
            ])
            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Text'),
                'name' => 'texto',
            ])
            @include('components.label-check', [
                'label' => __('Multiple'),
                'name' => 'multiple',
            ])
            @include('components.label-check', [
                'label' => __('Answered'),
                'name' => 'respondida',
            ])
            @include('components.label-check', [
                'label' => __('Correct'),
                'name' => 'correcta',
            ])
            @include('components.label-text', [
                'label' => __('Image'),
                'name' => 'imagen',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
