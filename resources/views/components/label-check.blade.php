<div class="row mb-3">
    <div class="col-sm-2">
        {{ html()->label($label, $name)->class('form-check-label') }}
    </div>
    <div class="col-sm-10">
        {{ html()->checkbox($name, old($name . '-checked'))->class('form-check-input') }}
        {{ html()->hidden($name . '-checked', old($name)) }}
    </div>
</div>
