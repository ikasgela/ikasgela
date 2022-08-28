@include('alumnos.partials.tarea.tutorial')

@if(Route::current()->getName() == 'archivo.show')
    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => trans('tutorial.archivada')
    ])
@endif

@section('fancybox')
    <link rel="stylesheet" href="{{ asset('/js/jquery.fancybox.min.css') }}"/>
    <script src="{{ asset('/js/jquery.fancybox.min.js') }}" defer></script>
@endsection

<div class="row">
    <div class="col-md-12">
        {{-- Tarjeta --}}
        <div class="card border-dark">
            @include('alumnos.partials.tarea.cabecera')
            <div class="card-body pb-1">
                <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
                    <div>
                        @include('actividades.partials.encabezado_con_etiquetas')
                        <p>{{ $actividad->descripcion }}</p>
                    </div>
                    @if(Auth::user()->hasRole('alumno') && !$actividad->hasEtiqueta('examen'))
                        @include('actividades.partials.boton_pregunta')
                    @elseif($actividad->hasEtiqueta('examen'))
                        @include('actividades.partials.puntuacion_examen')
                    @endif
                </div>
            </div>
            @if(!$actividad->auto_avance)
                @include('alumnos.partials.tarea.separador')
                @include('alumnos.partials.tarea.linea_progreso')
            @endif
            @switch($actividad->tarea->estado)
                @case(20)   {{-- Aceptada --}}
                @case(21)   {{-- Feedback leído --}}
                @if(!$actividad->is_expired)
                    <hr class="mt-0 mb-2">
                    @include('partials.tarjetas_actividad')
                @else
                    <div class="mb-2"></div>
                @endif
                @break
                @case(60)   {{-- Archivada --}}
                <hr class="mt-0 mb-2">
                @include('partials.tarjetas_actividad')
                @break
                @default
                    <div class="mb-0"></div>
            @endswitch
            @include('alumnos.partials.tarea.feedback')
            <hr class="my-0">
            <div class="card-body pb-1">
                @include('alumnos.partials.tarea.boton_accion')
            </div>
        </div>
        {{-- Fin tarjeta--}}
    </div>
</div>
