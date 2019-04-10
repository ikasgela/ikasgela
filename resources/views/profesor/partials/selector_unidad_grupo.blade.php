<div class="row mb-3">
    <div class="col-md-12">
        {!! Form::open(['route' => ['profesor.index']]) !!}

        <div class="form-group d-flex flex-row justify-content-between">
            {!! Form::label('unidad', __('Unit'), ['class' => 'col-form-label']) !!}
            <div class="flex-fill mx-3">
                <select class="form-control" id="unidad_id" name="unidad_id">
                    <option value="">{{ __('--- None ---') }}</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ session('grupo_unidad_actual') == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
