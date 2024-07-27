<div class="form-group row">
    {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox($name, $value, $value, array_merge(['class' => 'form-check-input ms-0 mt-2'], $attributes)) !!}
    </div>
</div>
