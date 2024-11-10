<div class="d-flex flex-row justify-content-between align-items-center">
    {{ html()->label(__('Course'), 'curso')->class('form-label m-0') }}
    <div class="flex-fill mx-3">
        <select class="form-select" id="curso_id" name="curso_id">
            <option value="-1">{{ __('--- None --- ') }}</option>
            @foreach($cursos as $curso)
                <option
                    value="{{ $curso->id }}" {{ session('filtrar_curso_actual') == $curso->id ? 'selected' : '' }}>
                    {{ $curso->full_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
    </div>
</div>
