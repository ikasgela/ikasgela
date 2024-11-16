<div class="d-flex flex-row justify-content-between align-items-center">
    {{ html()->label(__('User'), 'user')->class('form-label m-0') }}
    <div class="flex-fill mx-3">
        <select class="form-select" id="user_id" name="user_id">
            <option value="-1">{{ __('--- None --- ') }}</option>
            @foreach($users as $user)
                <option
                    value="{{ $user->id }}" {{ session('filtrar_user_actual') == $user->id ? 'selected' : '' }}>
                    {{ $user->full_name }} - {{ $user->email }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
    </div>
</div>
