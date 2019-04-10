@include('partials.subtitulo', ['subtitulo' => __('Available activities')])

@include('profesor.partials.selector_unidad_grupo')

@if(count($disponibles) > 0)
    <form id="asignar" method="POST" action="{{ route('profesor.asignar_tareas_grupo') }}">
        @csrf
        @include('profesor.partials.tabla_disponibles')
        @include('layouts.errors')
        <div class="mb-4">
            <button type="submit" class="btn btn-primary">{{ __('Save assigment') }}</button>
        </div>
    </form>
@else
    <div class="row">
        <div class="col-md">
            <p>No hay ninguna actividad disponible.</p>
        </div>
    </div>
@endif
