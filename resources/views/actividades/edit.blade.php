@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit activity')])

    <div class="card mb-3">
        <div class="card-body">
            {{ html()->modelForm($actividad, 'PUT', route('actividades.update', $actividad->id))->open() }}

            @include('components.label-select', [
                'label' => __('Unit'),
                'name' => 'unidad_id',
                'coleccion' => $unidades,
                'opcion' => function ($unidad) use ($actividad) {
                    return html()->option($unidad->full_name,
                        $unidad->id,
                        old('unidad_id', $actividad->unidad_id) == $unidad->id);
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
            ])
            @include('components.label-check', [
                'label' => __('Template'),
                'name' => 'plantilla',
            ])
            @include('components.label-text', [
                'label' => __('Order'),
                'name' => 'orden',
            ])
            @include('components.label-select', [
                'label' => __('Next'),
                'name' => 'siguiente_id',
                'coleccion' => $actividad->plantilla ? $plantillas : $actividades,
                'opcion' => function ($temp) use ($actividad) {
                    return html()->option("$temp->slug ($temp->id)",
                        $temp->id,
                        old('siguiente_id', $actividad->siguiente?->id) == $temp->id);
                },
                'default' => __('--- None ---'),
            ])
            @include('components.label-check', [
                'label' => __('Next field overriden'),
                'name' => 'siguiente_overriden',
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
                'opcion' => function ($qualification) use ($actividad) {
                    return html()->option($qualification->full_name,
                        $qualification->id,
                        old('qualification_id', $actividad->qualification_id) == $qualification->id);
                },
                'default' => __('--- None ---'),
            ])
            @include('components.label-datetime-clear', [
                'label' => __('Availability date'),
                'name' => 'fecha_disponibilidad',
            ])
            @include('components.label-datetime-clear', [
                'label' => __('Due date'),
                'name' => 'fecha_entrega',
            ])
            @include('components.label-datetime-clear', [
                'label' => __('Deadline'),
                'name' => 'fecha_limite',
            ])
            @include('components.label-datetime-readonly', [
                'label' => __('Start date'),
                'name' => 'fecha_comienzo',
            ])
            @include('components.label-datetime-readonly', [
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
            {{ html()->closeModelForm() }}
        </div>
    </div>
@endsection
