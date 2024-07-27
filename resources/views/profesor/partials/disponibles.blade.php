@include('partials.subtitulo', ['subtitulo' => __('Available activities')])

@include('profesor.partials.selector_unidad',['nombre_variable' => 'unidad_id_disponibles'])

@if($disponibles->count() > 0)
    <form method="POST" action="{{ route('profesor.asignar_tarea', ['user' => $user->id]) }}">
        @csrf
        @include('profesor.partials.tabla_disponibles')
        @include('layouts.errors')
        <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-4">
            <div>
                <button type="submit" class="btn btn-primary single_click">
                    <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Save assigment') }}
                </button>
                <a href="{{ route('profesor.index') }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            </div>
            <div>
                <input type="checkbox" id="notificar" name="notificar" checked="checked"/>
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
