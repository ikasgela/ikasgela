<div class="form-group d-flex flex-row justify-content-between">
    {!! Form::label('unidad', __('Unit'), ['class' => 'col-form-label']) !!}
    <div class="flex-fill mx-3">
        <select class="form-control" id="{{ $nombre_variable ?? 'unidad_id' }}"
                name="{{ $nombre_variable ?? 'unidad_id' }}">
            <option value="">{{ __('--- None ---') }}</option>
            @foreach($unidades as $unidad)
                <option
                    value="{{ $unidad->id }}" {{ session('profesor_'. $nombre_variable ?? 'unidad_id') == $unidad->id ? 'selected' : '' }}>
                    @isset($unidad->codigo)
                        {{ $unidad->codigo }} -
                    @endisset
                    {{ $unidad->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
    </div>
</div>
