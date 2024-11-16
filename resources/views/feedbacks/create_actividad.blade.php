@extends('layouts.app')

@section('tinymce')
    @include('feedbacks.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('New activity feedback message')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('feedbacks.save'))->open() }}

            @include('components.label-value', [
                'label' => __('Activity'),
                'name' => 'actividad_id',
                'value' => $actividad->full_name,
                'hidden' => $actividad->id,
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
