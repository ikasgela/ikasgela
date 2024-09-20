@include('partials.subtitulo', ['subtitulo' => __('Available activities')])

@include('profesor.partials.selector_unidad_grupo')

@if($disponibles->count() > 0)
    <form id="asignar" method="POST" action="{{ route('profesor.asignar_tareas_grupo') }}">
        @csrf
        @include('profesor.partials.tabla_disponibles')
        @include('layouts.errors')
        <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-4">
            <button type="submit" class="btn btn-primary single_click">
                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Save assigment') }}
            </button>
            <div>
                <input type="checkbox" id="fecha_override_enable" name="fecha_override_enable"/>
                <label class="ml-1" for="fecha_override_enable">{{ __('Override completion date') }}</label>
                <input class="mx-2" type="text" id="fecha_override" name="fecha_override"/>
                <input type="checkbox" id="notificar" name="notificar" checked="checked"/>
                <label class="ml-1" for="notificar">{{ __('Send notification email') }}</label>
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
