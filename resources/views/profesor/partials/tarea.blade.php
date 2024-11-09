@include('partials.tutorial', [
    'color' => 'success',
    'texto' => trans('tutorial.valorar_actividad')
])

@include('profesor.partials.tarjeta_usuario')

<div class="row mt-4">
    <div class="col-md-12">
        {{-- Tarjeta --}}
        <div class="card tarea-card mb-3">
            <div class="card-header text-white bg-dark d-flex justify-content-between">
                <span>{{ $actividad->unidad->curso->nombre }} » {{ $actividad->unidad->nombre }}</span>
            </div>
            <form class="col-md-12 p-0"
                  method="POST"
                  action="{{ route('actividades.estado', [$tarea->id]) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    @include('actividades.partials.encabezado_con_etiquetas')
                    <p>{{ $actividad->descripcion }}</p>
                    @include('profesor.partials.botonera-calificacion')
                    <div class="mt-2">
                        <i class="fas fa-bullhorn mt-3"></i>
                        <label class="m-0" for="feedback">{{ __('Feedback') }}</label>
                    </div>
                    <div class="border rounded p-3 mb-1">
                        @include('profesor.partials.selectores-feedback')
                        <textarea class="form-control"
                                  id="feedback"
                                  name="feedback"
                                  rows="15">{{ !is_null($tarea->feedback) ? $tarea->feedback : '' }}
                            <p>=== {{ __('Comments').' (v'.($tarea->intentos+1).')' }} ===</p>
                        </textarea>
                        @include('profesor.partials.formulario-feedback')
                    </div>
                </div>
            </form>
            {{ html()->form('POST', route('feedbacks.save'))->id('guardar_feedback')->open() }}
            <input form="guardar_feedback" type="hidden" id="mensaje" name="mensaje">
            <input form="guardar_feedback" type="hidden" name="curso_id" value="{{ $actividad->unidad->curso->id }}">
            @if(isset($actividad->original))
                <input form="guardar_feedback" type="hidden" name="actividad_id" value="{{ $actividad->original->id }}">
            @endif
            <input form="guardar_feedback" type="hidden" id="from" name="from" value="tarea">
            {{ html()->form()->close() }}
            @if($tarea->estado >= 10)
                @include('partials.tarjetas_actividad')
            @endif
            {{-- Fin tarjeta--}}
        </div>
    </div>
</div>
