<div class="row mb-3">
    <div class="col-2">
        {{ html()->label(__('Users'), 'users_seleccionados')->class('form-label') }}
    </div>
    <div class="col">
        <label class="mb-1">{{ __('Selected') }}</label>
        <select id="users-select1" name="users_seleccionados[]"
                class="form-select" multiple @style(['height:10em'])>
            @foreach($users_seleccionados as $user)
                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-1 d-flex flex-row justify-content-center align-items-center mt-3">
        <button data-selector="users" type="button" class="btn btn-primary btn-sm add">
            <i class="bi bi-arrow-left"></i>
        </button>
        <button data-selector="users" type="button" class="btn btn-primary btn-sm ms-1 remove">
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
    <div class="col">
        <label class="mb-1">{{ __('Available') }}</label>
        <select id="users-select2"
                class="form-select" multiple @style(['height:10em'])>
            @foreach($users_disponibles as $user)
                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
            @endforeach
        </select>
    </div>
</div>
