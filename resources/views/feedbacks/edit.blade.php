@extends('layouts.app')

@section('tinymce')
    @include('feedbacks.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('Edit feedback message')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($feedback, 'PUT', route('feedbacks.update', $feedback->id))->open() }}

            @include('components.label-value', [
                'label' => is_a($feedback->comentable, 'App\Models\Curso') ? __('Course') : __('Activity'),
                'name' => 'comentable_id',
                'value' => $feedback->comentable->full_name,
                'hidden' => $feedback->comentable_id,
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
                              name="mensaje">{!! old('mensaje', $feedback->mensaje) !!}</textarea>
                </div>
            </div>

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
