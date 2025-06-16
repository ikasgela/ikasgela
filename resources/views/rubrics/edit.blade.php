@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit rubric')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($rubric, 'PUT', route('rubrics.update', $rubric->id))->open() }}

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
                'label' => trans_choice("tasks.completed", 1),
                'name' => 'completada',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
