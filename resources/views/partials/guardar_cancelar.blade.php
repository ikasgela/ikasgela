<div class="form-group">
    <button id="boton_guardar" type="submit" class="btn btn-primary text-light single_click">
        <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ isset($texto)? $texto : __('Save') }}</button>
    <a href="{!! anterior() !!}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
</div>
