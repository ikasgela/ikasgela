<div class="form-group row">
    {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label']) !!}
    <div class="col-sm-10 form-control-plaintext">
        {!! $value ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}
    </div>
</div>
