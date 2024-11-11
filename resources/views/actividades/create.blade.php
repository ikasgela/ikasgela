@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New activity')])

    <div class="card mb-3">
        <div class="card-body">
            {{ html()->form('POST', route('actividades.store'))->open() }}

            @include('components.label-select', [
                'label' => __('Unit'),
                'name' => 'unidad_id',
                'coleccion' => $unidades,
                'opcion' => function ($unidad) {
                        return html()->option($unidad->full_name,
                            $unidad->id,
                            old('unidad_id', session('profesor_unidad_id_disponibles')) == $unidad->id);
                },
            ])
            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'nombre',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-text', [
                'label' => __('Slug'),
                'name' => 'slug',
            ])
            @include('components.label-text', [
                'label' => __('Score'),
                'name' => 'puntuacion',
                'value' => 100,
            ])
            @include('components.label-check', [
                'label' => __('Template'),
                'name' => 'plantilla',
                'checked' => true,
            ])
            @include('components.label-select', [
                'label' => __('Next'),
                'name' => 'siguiente_id',
                'coleccion' => $actividades,
                'opcion' => function ($temp) {
                        return html()->option("$temp->slug ($temp->id)",
                            $temp->id);
                },
                'default' => __('--- None ---'),
            ])
            @include('components.label-check', [
                'label' => __('Final'),
                'name' => 'final',
            ])
            @include('components.label-check', [
                'label' => __('Auto advance'),
                'name' => 'auto_avance',
            ])
            @include('components.label-select', [
                'label' => __('Qualification'),
                'name' => 'qualification_id',
                'coleccion' => $qualifications,
                'opcion' => function ($qualification) {
                        return html()->option($qualification->full_name,
                            $qualification->id);
                },
                'default' => __('--- None ---'),
            ])
            @include('components.label-text', [
                'label' => __('Availability date'),
                'name' => 'fecha_disponibilidad',
            ])
            @include('components.label-text', [
                'label' => __('Due date'),
                'name' => 'fecha_entrega',
            ])
            @include('components.label-text', [
                'label' => __('Deadline'),
                'name' => 'fecha_limite',
            ])
            @include('components.label-text', [
                'label' => __('Start date'),
                'name' => 'fecha_comienzo',
            ])
            @include('components.label-text', [
                'label' => __('Completion date'),
                'name' => 'fecha_finalizacion',
            ])
            @include('components.label-check', [
                'label' => __('Highlighted'),
                'name' => 'destacada',
            ])
            @include('components.label-text', [
                'label' => __('Tags'),
                'name' => 'tags',
            ])
            @include('components.label-text', [
                'label' => __('Multiplier'),
                'name' => 'multiplicador',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}
        </div>
    </div>
@endsection
