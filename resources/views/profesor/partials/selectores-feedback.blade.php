<div class="row mb-3">
    <div class="col-md-6 d-flex justify-content-between align-items-center mb-3 mb-md-0">
        {{ html()->label(__('Course'), 'curso')->class('col-form-label') }}
        <div class="flex-fill mx-3">
            <select class="form-select" id="feedback_id" name="feedback_id">
                {{--                                <option value="">{{ __('--- None ---') }}</option>--}}
                @foreach($feedbacks_curso as $feedback)
                    <option
                        data-mensaje="{{ $feedback->mensaje }}"
                        value="{{ $feedback->id }}" {{ session('profesor_feedback_actual') == $feedback->id ? 'selected' : '' }}>
                        {{ $feedback->titulo }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="button" id="boton_feedback"
                    {{ $feedbacks_curso->count() == 0 ? 'disabled' : '' }}
                    class="btn btn-primary">{{ __('Add') }}</button>
        </div>
    </div>
    <div class="col-md-6 d-flex justify-content-between align-items-center">
        {{ html()->label(__('Activity'), 'actividad')->class('col-form-label') }}
        <div class="flex-fill mx-3">
            <select class="form-select" id="feedback_actividad_id"
                    name="feedback_actividad_id">
                {{--                                <option value="">{{ __('--- None ---') }}</option>--}}
                @foreach($feedbacks_actividad as $feedback)
                    <option
                        data-mensaje="{{ $feedback->mensaje }}"
                        value="{{ $feedback->id }}" {{ session('profesor_feedback_actividad_actual') == $feedback->id ? 'selected' : '' }}>
                        {{ $feedback->titulo }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="button" id="boton_feedback_actividad"
                    {{ $feedbacks_actividad->count() == 0 ? 'disabled' : '' }}
                    class="btn btn-primary">{{ __('Add') }}</button>
        </div>
    </div>
</div>
