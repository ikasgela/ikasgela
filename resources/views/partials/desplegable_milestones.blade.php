<div class="d-flex justify-content-between align-items-end mb-3">
    {{ html()->label(__('Milestone'), 'milestone_id')->class('form-label') }}
    <select class="form-select mx-3" id="milestone_id" name="milestone_id">
        <option value="-1">{{ __('--- None (whole course) ---') }}</option>
        @foreach($milestones as $milestone)
            <option
                value="{{ $milestone->id }}" {{ session('filtrar_milestone_actual') == $milestone->id ? 'selected' : '' }}>
                {{ $milestone->name }} {{ !$milestone->published ? '(' . __('not published') . ')' : '' }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary text-light">{{ __('Show') }}</button>
</div>
