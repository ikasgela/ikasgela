@include('partials.subtitulo', ['subtitulo' => __('Available activities')])

@include('teams.partials.selector_unidad_grupo')

@if($disponibles->count() > 0)
    <form id="asignar" method="POST" action="{{ route('profesor.asignar_tareas_equipo') }}">
        @csrf
        @include('profesor.partials.tabla_disponibles')
        @include('layouts.errors', ['margenes' => 'mt-0 mb-3'])
        <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-4">
            <button type="submit" class="btn btn-primary single_click">
                <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Save assigment') }}
            </button>
            <div>
                <input class="form-check-input" type="checkbox" id="notificar" name="notificar" checked="checked"/>
                <label class="ms-1" for="notificar">{{ __('Send notification email') }}</label>
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
