<div class="row mb-3">
    <div class="col-md-12">
        {!! Form::open(['route' => ['alumnos.tareas', $user->id]]) !!}

        <div class="form-group d-flex flex-row justify-content-between">
            {!! Form::label('unidad', __('Unit'), ['class' => 'col-form-label']) !!}
            <div class="flex-fill mx-3">
                <select class="form-control" id="unidad_id" name="unidad_id">
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ session('profesor_unidad_actual') == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-3 mt-sm-0">
                <button type="submit" class="btn btn-primary">{{ __('Change') }}</button>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
