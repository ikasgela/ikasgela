<div class="form-group d-flex flex-row justify-content-between">
    {!! Form::label('milestone_id', __('Milestone'), ['class' => 'col-form-label']) !!}
    <div class="flex-fill mx-3">
        <select class="custom-select" id="milestone_id" name="milestone_id">
            <option value="-1">{{ __('--- None (whole course) ---') }}</option>
            @foreach($milestones as $milestone)
                <option
                    value="{{ $milestone->id }}" {{ session('filtrar_milestone_actual') == $milestone->id ? 'selected' : '' }}>
                    {{ $milestone->name }} {{ !$milestone->published ? '(' . __('not published') . ')' : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <button type="submit" class="btn btn-primary">{{ __('Show') }}</button>
    </div>
</div>
