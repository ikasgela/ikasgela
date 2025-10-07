<div class="row mb-3">
    <div class="col-sm-2">
        {{ html()->label($label, $name)->class('col-form-label') }}
    </div>
    <div class="col-sm-10">
        {{ html()->datetime($name, $value ?? null)->placeholder($placeholder ?? null)->class('form-control') }}
    </div>
</div>
