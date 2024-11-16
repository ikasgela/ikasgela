@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit question')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($pregunta, 'PUT', route('preguntas.update', $pregunta->id))->open() }}

            @include('components.label-select', [
                'label' => __('Questionnaire'),
                'name' => 'cuestionario_id',
                'coleccion' => $cuestionarios,
                'opcion' => function ($cuestionario) use ($pregunta) {
                        return html()->option($cuestionario->titulo,
                            $cuestionario->id,
                            old('cuestionario_id', $pregunta->cuestionario_id) == $cuestionario->id);
                },
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
            @include('components.label-text', [
                'label' => __('Order'),
                'name' => 'orden',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Items')])

    @include('items.tabla')

    <div class="mb-3">
        <a class="btn btn-primary"
           href="{{ route('items.anyadir', $pregunta) }}">{{ __('Add item') }}</a>
    </div>

@endsection
