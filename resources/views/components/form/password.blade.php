<div class="form-group row">
    {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label']) !!}
    <div class="col-sm-10">
        {!! Form::password($name, array_merge(['class' => 'form-control'], $attributes)) !!}
    </div>
</div>
