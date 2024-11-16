@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New skill')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('skills.store'))->open() }}

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
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
