<div class="row mb-3">
    <div class="col-sm-2">
        {{ html()->label($label, $name)->class('col-form-label') }}
    </div>
    <div class="col-sm-10 d-flex">
        {{ html()->datetime($name, $value ?? null)->placeholder($placeholder ?? null)->class('form-control') }}
        {{ html()->button(__("Clear"))->class('btn btn-secondary ms-3')
            ->type('button')
            ->attribute('onclick', "document.getElementsByName('$name')[0].value=''; document.getElementsByName('$name')[0].type='text'; document.getElementsByName('$name')[0].type='datetime-local';")
        }}
    </div>
</div>
