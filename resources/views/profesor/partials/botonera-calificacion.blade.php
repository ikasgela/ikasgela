<div class="d-flex align-items-center border rounded p-3 mb-3">
    @include('profesor.partials.selector_tarea')
    @if($tarea->estado == 11)
        <button type="submit" name="nuevoestado" value="10"
                class="btn btn-secondary single_click">
            <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Show') }}
        </button>
    @else
        <button type="submit" name="nuevoestado" value="31"
                class="btn btn-secondary single_click">
            <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Reset') }}
        </button>
    @endif
    <label class="mx-3">{{ __('Attempts') }}: {{ $tarea->intentos }}</label>
    <button type="submit" name="nuevoestado" value="41"
            class="me-3 btn btn-warning single_click"
            onclick="return validate_feedback();">
        <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Send again') }}
    </button>
    <label class="me-2">{{ __('Score') }}</label>
    <input class="me-2 form-control"
           type="number" min="0" max="100" step="1"
           style="flex-basis: 8em"
           name="puntuacion"
           value="{{ !is_null($tarea->puntuacion) ? $tarea->puntuacion : $actividad->puntuacion }}"/>
    <label class="me-3"> {{ __('over') }} {{ $actividad->puntuacion }}</label>
    <button type="submit" name="nuevoestado" value="40"
            class="btn btn-primary me-3 single_click"
            onclick="return validate_feedback();">
        <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Finished') }}
    </button>
</div>
@if(!is_null($actividad->siguiente))
    <div class="d-flex justify-content-end align-items-center mb-3">
        <label>{{ __('Next') }}: @include('actividades.partials.siguiente')</label>
        @if($actividad->final)
            <button type="submit" name="nuevoestado" value="70"
                    class="mx-3 btn btn-light single_click">
                <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Resume') }}
            </button>
        @else
            <button type="submit" name="nuevoestado" value="70"
                    class="mx-3 btn btn-light single_click">
                <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Pause') }}
            </button>
        @endif
        <button type="submit" name="nuevoestado" value="71"
                class="btn btn-light single_click">
            <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Show next') }}
        </button>
    </div>
@endif
@if($actividad->is_expired)
    <div class="d-flex justify-content-end align-items-center mb-3">
        <button type="submit" name="nuevoestado" value="62"
                class="btn btn-light single_click">
            <span class="spinner-border spinner-border-sm"
                  style="display:none;"></span> {{ __('Archive expired') }}
        </button>
        <button type="submit" name="nuevoestado" value="63"
                class="btn btn-light ms-3 single_click">
            <span class="spinner-border spinner-border-sm"
                  style="display:none;"></span> {{ __('Extend deadline') }}
        </button>
        <label class="mx-2">{{ __('by') }}</label>
        <input class="me-2 form-control"
               type="number" min="0" max="90" step="1"
               style="flex-basis: 8em"
               name="ampliacion_plazo"
               value="{{ $actividad->unidad->curso->plazo_actividad ?? 7 }}"/>
        <label class="me-2">{{ __('days') }}.</label>
    </div>
@endif
