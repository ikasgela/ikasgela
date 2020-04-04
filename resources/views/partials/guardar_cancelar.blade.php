<div class="form-group">
    <button type="submit" class="btn btn-primary single_click">
        <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ isset($texto)? $texto : __('Save') }}</button>
    <a href="{!! anterior() !!}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
</div>
