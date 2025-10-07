<div class="row mb-3">
    <div class="col-sm-2">
        {{ html()->label($label, $name)->class('col-form-label') }}
    </div>
    <div class="col-sm-10">
        @if(!is_null(html()->value($name)))
            {{ html()->datetime($name)->isReadonly()->class('form-control-plaintext') }}
        @else
            <span class="form-control-plaintext">{{ __('Undefined') }}</span>
        @endif
    </div>
</div>
