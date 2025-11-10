@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit test result')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($test_result, 'PUT', route('test_results.update', $test_result->id))->open() }}

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
            @include('components.label-text', [
                'label' => __('Number of right answers'),
                'name' => 'num_correctas',
            ])
            @include('components.label-text', [
                'label' => __('Number of wrong answers'),
                'name' => 'num_incorrectas',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
