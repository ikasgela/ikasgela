@include('partials.subtitulo', ['subtitulo' => __('Available activities')])

@include('teams.partials.selector_unidad',['nombre_variable' => 'unidad_id_disponibles'])

@if($disponibles->count() > 0)
    <form method="POST" action="{{ route('profesor.asignar_tarea_equipo', ['team' => $team->id]) }}">
        @csrf
        @include('profesor.partials.tabla_disponibles')
        @include('layouts.errors', ['margenes' => 'mt-0 mb-3'])
        <div class="d-flex justify-content-between mb-3">
            <div>
                <button type="submit" class="btn btn-primary single_click">
                    <span class="spinner-border spinner-border-sm"
                          style="display:none;"></span> {{ __('Save assigment') }}
                </button>
                <a href="{{ route('profesor.index') }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            </div>
            <div class="d-flex justify-content-end align-items-center" style="flex-grow: 2">
                <input class="form-check-input me-2 my-0" type="checkbox"
                       id="fecha_override_enable" name="fecha_override_enable"/>
                <label class="form-check-label" for="fecha_override_enable">{{ __('Override completion date') }}</label>
                <input class="form-control ms-2 me-3" style="flex-basis: 16em" type="datetime-local"
                       id="fecha_override" name="fecha_override"/>
                <input class="form-check-input me-2 my-0" type="checkbox" id="notificar" name="notificar"
                       checked="checked"/>
                <label class="form-check-label" for="notificar">{{ __('Send notification email') }}</label>
            </div>
        </div>
    </form>
@else
    <div class="row">
        <div class="col-md">
            <p>No hay ninguna actividad disponible.</p>
        </div>
    </div>
@endif
