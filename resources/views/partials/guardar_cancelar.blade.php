<div class="form-group">
    <button id="boton_guardar" type="submit" class="btn btn-primary single_click">
        <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ isset($texto)? $texto : __('Save') }}</button>
    <a href="{!! anterior() !!}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
</div>
