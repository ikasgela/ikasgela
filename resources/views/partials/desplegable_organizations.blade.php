<div class="form-group d-flex flex-row justify-content-between">
    {!! Form::label('organization_id', __('Organization'), ['class' => 'col-form-label']) !!}
    <div class="flex-fill mx-3">
        <select class="custom-select" id="organization_id" name="organization_id">
            <option value="-1">{{ __('--- None --- ') }}</option>
            @foreach($organizations as $organization)
                <option
                    value="{{ $organization->id }}" {{ session('filtrar_organization_actual') == $organization->id ? 'selected' : '' }}>
                    {{ $organization->full_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
    </div>
</div>
