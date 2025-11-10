@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New test result')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('test_results.store'))->open() }}

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
            @include('components.label-text', [
                'label' => __('Number of questions'),
                'name' => 'num_preguntas',
            ])
            @include('components.label-text', [
                'label' => __('Right answer value'),
                'name' => 'valor_correcta',
                'placeholder' => '1.0',
            ])
            @include('components.label-text', [
                'label' => __('Wrong answer value'),
                'name' => 'valor_incorrecta',
                'placeholder' => '-0.25',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
