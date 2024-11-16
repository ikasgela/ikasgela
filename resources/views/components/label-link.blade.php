<div class="row mb-3">
    <div class="col-sm-2">
        {{ html()->label($label)->class('col-form-label') }}
    </div>
    <div class="col-sm-10">
        {{ html()->a($link, $value)->class('btn btn-link px-0')->target($target ?? null) }}
    </div>
</div>
