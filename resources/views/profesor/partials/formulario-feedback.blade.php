<div class="d-flex justify-content-end align-items-center mt-3">
    <label class="me-2">{{ __('Title') }}</label>
    <div class="flex-fill">
        <input class="form-control me-2" form="guardar_feedback" type="text" id="titulo" name="titulo">
    </div>
    <label class="mx-2">{{ __('save as') }}</label>
    <button form="guardar_feedback" type="submit" name="tipo" value="curso"
            class="btn btn-primary text-light">{{ __('course feedback') }}
    </button>
    @if(isset($actividad->original))
        <label class="mx-2">{{ __('or') }}</label>
        <button form="guardar_feedback" type="submit" name="tipo" value="actividad"
                class="btn btn-primary text-light">{{ __('activity feedback') }}
        </button>
    @endif
</div>
