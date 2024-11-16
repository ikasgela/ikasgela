@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit item')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($item, 'PUT', route('items.update', $item->id))->open() }}

            @include('components.label-select', [
                'label' => __('Question'),
                'name' => 'pregunta_id',
                'coleccion' => $preguntas,
                'opcion' => function ($pregunta) use ($item) {
                        return html()->option($pregunta->titulo,
                            $pregunta->id,
                            old('pregunta_id', $item->pregunta_id) == $pregunta->id);
                },
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
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
