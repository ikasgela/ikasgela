@include('partials.subtitulo', ['subtitulo' => __('Available activities')])

@include('profesor.partials.selector_unidad_grupo')

@if($disponibles->count() > 0)
    <form id="asignar" method="POST" action="{{ route('profesor.asignar_tareas_grupo') }}">
        @csrf
        @include('profesor.partials.tabla_disponibles')
        @include('layouts.errors')
        <div class="d-flex justify-content-between mb-4">
            <div style="flex-grow: 1">
                <button type="submit" class="btn btn-primary text-light single_click">
                    <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Save assigment') }}
                </button>
            </div>
            <div class="d-flex justify-content-end align-items-center" style="flex-grow: 2">
                <input class="form-check-input me-2 my-0" type="checkbox"
                       id="fecha_override_enable" name="fecha_override_enable"/>
                <label class="form-check-label" for="fecha_override_enable">{{ __('Override completion date') }}</label>
                <input class="form-control ms-2 me-3" style="flex-basis: 16em" type="text"
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
