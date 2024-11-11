<div class="row mb-3">
    <div class="col-2">
        {{ html()->label(__('Courses'), 'cursos_seleccionados')->class('form-label') }}
    </div>
    <div class="col">
        <label>{{ __('Selected') }}</label>
        <select name="cursos_seleccionados[]" multiple class="form-control multi-select"
                id="cursos-select1">
            @foreach($cursos_seleccionados as $curso)
                <option value="{{ $curso->id }}">{{ $curso->nombre }}
                    - {{ $curso->category->period->name }}
                    ({{ $curso->category->period->organization->name }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-1 d-flex flex-row justify-content-center align-items-center mt-3">
        <button data-selector="cursos" type="button" class="btn btn-primary btn-sm add">
            <i class="fas fa-arrow-left"></i>
        </button>
        <button data-selector="cursos" type="button" class="btn btn-primary btn-sm ms-1 remove">
            <i class="fas fa-arrow-right"></i>
        </button>
    </div>
    <div class="col">
        <label>{{ __('Available') }}</label>
        <select multiple class="form-control multi-select" id="cursos-select2">
            @foreach($cursos_disponibles as $curso)
                <option value="{{ $curso->id }}">{{ $curso->nombre }}
                    - {{ $curso->category->period->name }}
                    ({{ $curso->category->period->organization->name }})
                </option>
            @endforeach
        </select>
    </div>
</div>
