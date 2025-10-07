@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit unit')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($unidad, 'PUT', route('unidades.update', $unidad->id))->open() }}

            @include('components.label-select', [
                'label' => __('Course'),
                'name' => 'curso_id',
                'coleccion' => $cursos,
                'opcion' => function ($curso) use ($unidad) {
                        return html()->option($curso->full_name,
                            $curso->id,
                            old('curso_id', $unidad->curso_id) == $curso->id);
                },
            ])

            @include('components.label-text', [
                'label' => __('Code'),
                'name' => 'codigo',
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

            @include('components.label-select', [
                'label' => __('Qualification'),
                'name' => 'qualification_id',
                'coleccion' => $qualifications,
                'opcion' => function ($qualification) use ($unidad) {
                        return html()->option($qualification->full_name,
                            $qualification->id,
                            old('qualification_id', $unidad->qualification_id) == $qualification->id);
                },
                'default' => __('--- None ---'),
            ])

            @include('components.label-text', [
                'label' => __('Order'),
                'name' => 'orden',
            ])
            @include('components.label-text', [
                'label' => __('Tags'),
                'name' => 'tags',
            ])

            @include('components.label-datetime', [
                'label' => __('Availability date'),
                'name' => 'fecha_disponibilidad',
            ])
            @include('components.label-datetime', [
                'label' => __('Due date'),
                'name' => 'fecha_entrega',
            ])
            @include('components.label-datetime', [
                'label' => __('Deadline'),
                'name' => 'fecha_limite',
            ])

            @include('components.label-text', [
                'label' => __('Minimum percent'),
                'name' => 'minimo_entregadas',
            ])

            @include('components.label-check', [
                'label' => __('Visible'),
                'name' => 'visible',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
