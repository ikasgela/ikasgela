@include('partials.subtitulo', ['subtitulo' => __('Available activities')])

@include('profesor.partials.selector_unidad')

@if($disponibles->count() > 0)
    <form method="POST" action="{{ route('profesor.asignar_tarea', ['user' => $user->id]) }}">
        @csrf
        @include('profesor.partials.tabla_disponibles')
        @include('layouts.errors')
        <div class="mb-4">
            <button type="submit" class="btn btn-primary">{{ __('Save assigment') }}</button>
            <a href="{{ route('profesor.index') }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
@else
    <div class="row">
        <div class="col-md">
            <p>No hay ninguna actividad disponible.</p>
        </div>
    </div>
@endif
