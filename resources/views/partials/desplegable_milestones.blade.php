<div class="d-flex flex-row justify-content-between align-items-center">
    {{ html()->label(__('Milestone'), 'milestone_id')->class('form-label m-0') }}
    <select class="form-select mx-3" id="milestone_id" name="milestone_id">
        <option value="-1">{{ __('--- None (whole course) ---') }}</option>
        @foreach($milestones as $milestone)
            <option
                value="{{ $milestone->id }}" {{ session('filtrar_milestone_actual') == $milestone->id ? 'selected' : '' }}>
                {{ $milestone->name }} {{ !$milestone->published ? '(' . __('not published') . ')' : '' }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary">{{ __('Show') }}</button>
</div>
