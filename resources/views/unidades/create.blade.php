@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New unit')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('unidades.store'))->open() }}

            @include('components.label-select', [
                'label' => __('Course'),
                'name' => 'curso_id',
                'coleccion' => $cursos,
                'opcion' => function ($curso) {
                        return html()->option($curso->full_name,
                            $curso->id,
                            old('curso_id', Auth::user()->curso_actual()?->id) == $curso->id);
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
                'opcion' => function ($qualification) {
                        return html()->option($qualification->full_name,
                            $qualification->id,
                            old('qualification_id') == $qualification->id);
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
                'label' => __('Minimum percent'),
                'name' => 'minimo_entregadas',
            ])

            @include('components.label-check', [
                'label' => __('Visible'),
                'name' => 'visible',
                'checked' => true,
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
