@extends('layouts.app')

@section('tinymce')
    @include('feedbacks.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('New course feedback message')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('feedbacks.store'))->open() }}

            @include('components.label-select', [
                'label' => __('Course'),
                'name' => 'comentable_id',
                'coleccion' => $cursos,
                'opcion' => function ($curso) use ($curso_actual){
                        return html()->option($curso->full_name,
                            $curso->id,
                            old('curso_id', $curso_actual?->id) == $curso->id);
                },
            ])

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])

            <div class="form-group row mb-3">
                <div class="col-2">
                    {{ html()->label(__('Message'), 'mensaje')->class('col-form-label') }}
                </div>
                <div class="col-10">
                    <textarea rows="25" class="form-control" id="mensaje"
                              name="mensaje">{{ old('mensaje') }}</textarea>
                </div>
            </div>

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
