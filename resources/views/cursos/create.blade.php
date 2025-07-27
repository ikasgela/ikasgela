@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New course'), 'subtitulo' => ''])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('cursos.store'))->open() }}

            @include('components.label-select', [
                'label' => __('Category'),
                'name' => 'category_id',
                'coleccion' => $categories,
                'opcion' => function ($category) {
                        return html()->option($category->full_name,
                            $category->id,
                            old('category_id') == $category->id);
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
                'label' => __('Gitea organization'),
                'name' => 'gitea_organization',
            ])
            @include('components.label-text', [
                'label' => __('Tags'),
                'name' => 'tags',
            ])
            @include('components.label-check', [
                'label' => __('Open enrollment'),
                'name' => 'matricula_abierta',
            ])

            @include('components.label-select', [
                'label' => __('Qualification'),
                'name' => 'qualification_id',
                'default' => __('--- None ---'),
                'disabled' => true,
            ])

            @include('components.label-text', [
                'label' => __('Simultaneous activities'),
                'name' => 'max_simultaneas',
                'value' => 10,
            ])
            @include('components.label-text', [
                'label' => __('Activity deadline'),
                'name' => 'plazo_actividad',
                'value' => 7,
            ])

            @include('components.label-text', [
                'label' => __('Minimum completed percent'),
                'name' => 'minimo_entregadas',
                'value' => 80,
            ])
            @include('components.label-text', [
                'label' => __('Minimum skills percent'),
                'name' => 'minimo_competencias',
                'value' => 50,
            ])
            @include('components.label-text', [
                'label' => __('Minimum exams percent'),
                'name' => 'minimo_examenes',
                'value' => 50,
            ])
            @include('components.label-text', [
                'label' => __('Minimum final exams percent'),
                'name' => 'minimo_examenes_finales',
                'value' => 50,
            ])
            @include('components.label-check', [
                'label' => __('Mandatory exams'),
                'name' => 'examenes_obligatorios',
                'checked' => true,
            ])
            @include('components.label-text', [
                'label' => __('Maximum recoverable percent'),
                'name' => 'maximo_recuperable_examenes_finales',
                'value' => 100,
            ])

            @include('components.label-text', [
                'label' => __('Start date'),
                'name' => 'fecha_inicio',
            ])
            @include('components.label-text', [
                'label' => __('End date'),
                'name' => 'fecha_fin',
            ])

            @include('components.label-check', [
                'label' => __('Show course progress'),
                'name' => 'progreso_visible',
            ])
            @include('components.label-check', [
                'label' => __('Silence notifications'),
                'name' => 'silence_notifications',
            ])
            @include('components.label-check', [
                'label' => __('Normalize calification'),
                'name' => 'normalizar_nota',
            ])

            <div class="row mb-3">
                <div class="col-sm-2">
                    {{ html()->label(__('Proportional calification adjustment'), 'ajuste_proporcional_nota')->class('col-form-label') }}
                </div>
                <div class="col-sm-10">
                    {{ html()->select('ajuste_proporcional_nota')->class('form-select')->open() }}
                    {{ html()->option(__('--- None --- ')) }}
                    {{ html()->option(__('Average'), 'media', old('ajuste_proporcional_nota') == 'media') }}
                    {{ html()->option(__('Median'), 'mediana', old('ajuste_proporcional_nota') == 'mediana') }}
                    {{ html()->select()->close() }}
                </div>
            </div>

            @include('components.label-check', [
                'label' => __('Show califications'),
                'name' => 'mostrar_calificaciones',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
