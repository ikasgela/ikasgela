@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit skill')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($skill, 'PUT', route('skills.update', $skill->id))->open() }}

            @include('components.label-select', [
                'label' => __('Course'),
                'name' => 'curso_id',
                'coleccion' => $cursos,
                'opcion' => function ($curso) use ($skill) {
                        return html()->option($curso->full_name,
                            $curso->id,
                            old('curso_id', $skill->curso_id) == $curso->id);
                },
            ])

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'description',
            ])
            @include('components.label-text', [
                'label' => __('Exam weight'),
                'name' => 'peso_examen',
            ])
            @include('components.label-text', [
                'label' => __('Minimum percent'),
                'name' => 'minimo_competencias',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
