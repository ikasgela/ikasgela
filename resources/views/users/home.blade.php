@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Escritorio'])

    @if(count($actividades) > 0)
        @php($num_actividad = 1)
        @foreach($actividades as $actividad)
            @include('tareas.show')
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
