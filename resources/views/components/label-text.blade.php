<div class="row mb-3">
    <div class="col-sm-2 d-flex align-items-end">
        {{ html()->label($label, $name)->class('form-label') }}
    </div>
    <div class="col-sm-10">
        {{ html()->text($name, $value ?? null)->class('form-control') }}
    </div>
</div>
