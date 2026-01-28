@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New rubric')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('rubrics.store'))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-check', [
                'label' => __('Show title'),
                'name' => 'titulo_visible',
                'checked' => true
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-check', [
                'label' => __('Show description'),
                'name' => 'descripcion_visible',
                'checked' => true
            ])
            @include('components.label-check', [
                'label' => __('Template'),
                'name' => 'plantilla',
                'checked' => true,
            ])
            @include('components.label-check', [
                'label' => trans_choice("tasks.completed", 1),
                'name' => 'completada',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
