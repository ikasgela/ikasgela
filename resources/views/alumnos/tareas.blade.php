@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Desktop') }}</h1>
        </div>
        <div>
            @if(session('num_actividades') > 0)
                @if(session('num_actividades') == 1)
                    <h2 class="text-muted font-xl">Tienes una actividad en curso</h2>
                @else
                    <h2 class="text-muted font-xl">Tienes {{ session('num_actividades') }} actividades en curso</h2>
                @endif
            @endif
        </div>
    </div>

    @if(count($actividades) > 0)
        @php($num_actividad = 1)
        @foreach($actividades as $actividad)
            @include('alumnos.partials.tarea')
            @php($num_actividad+=1)
        @endforeach
    @else
        @if(session('tutorial'))
            <div class="callout callout-success b-t-1 b-r-1 b-b-1">
                <small class="text-muted">{{ __('Tutorial') }}</small>
                <p>Aquí aparecerán las actividades que tengas asignadas.</p>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <p>No tienes tareas asignadas.</p>
            </div>
        </div>
    @endif
@endsection
