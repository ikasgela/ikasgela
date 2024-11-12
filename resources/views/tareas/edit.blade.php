@extends('layouts.app')

@section('tinymce')
    @include('profesor.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('Edit task')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($tarea, 'PUT', route('tareas.update', $tarea->id))->open() }}

            @include('components.label-text', [
                'label' => __('Status'),
                'name' => 'estado',
            ])

            <div class="row mb-3">
                <div class="col-sm-2">
                    {{ html()->label(__('Feedback'), 'feedback')->class('col-form-label') }}
                </div>
                <div class="col-sm-10">
                    <textarea class="form-control"
                              id="feedback"
                              name="feedback"
                              rows="15">
                        {{ !is_null($tarea->feedback) ? $tarea->feedback : '' }}
                    </textarea>
                </div>
            </div>

            @include('components.label-text', [
                'label' => __('Score'),
                'name' => 'puntuacion',
            ])
            @include('components.label-text', [
                'label' => __('Attempts'),
                'name' => 'intentos',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
