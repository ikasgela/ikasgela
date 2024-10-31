@include('partials.tutorial', [
    'color' => 'success',
    'texto' => trans('tutorial.valorar_actividad')
])

@include('profesor.partials.tarjeta_usuario')

<div class="row mt-4">
    <div class="col-md-12">
        {{-- Tarjeta --}}
        <div class="card border-dark">
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
                        <div class="row">
                            <div class="col-md-6 form-group d-flex flex-row justify-content-between">
                                {!! Form::label('unidad', __('Course'), ['class' => 'col-form-label']) !!}
                                <div class="flex-fill mx-3">
                                    <select class="form-control" id="feedback_id" name="feedback_id">
                                        {{--                                <option value="">{{ __('--- None ---') }}</option>--}}
                                        @foreach($feedbacks_curso as $feedback)
                                            <option
                                                data-mensaje="{{ $feedback->mensaje }}"
                                                value="{{ $feedback->id }}" {{ session('profesor_feedback_actual') == $feedback->id ? 'selected' : '' }}>
                                                {{ $feedback->titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <button type="button" id="boton_feedback"
                                            {{ $feedbacks_curso->count() == 0 ? 'disabled' : '' }}
                                            class="btn btn-primary">{{ __('Add') }}</button>
                                </div>
                            </div>
                            <div class="col-md-6 form-group d-flex flex-row justify-content-between">
                                {!! Form::label('unidad', __('Activity'), ['class' => 'col-form-label']) !!}
                                <div class="flex-fill mx-3">
                                    <select class="form-control" id="feedback_actividad_id"
                                            name="feedback_actividad_id">
                                        {{--                                <option value="">{{ __('--- None ---') }}</option>--}}
                                        @foreach($feedbacks_actividad as $feedback)
                                            <option
                                                data-mensaje="{{ $feedback->mensaje }}"
                                                value="{{ $feedback->id }}" {{ session('profesor_feedback_actividad_actual') == $feedback->id ? 'selected' : '' }}>
                                                {{ $feedback->titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <button type="button" id="boton_feedback_actividad"
                                            {{ $feedbacks_actividad->count() == 0 ? 'disabled' : '' }}
                                            class="btn btn-primary">{{ __('Add') }}</button>
                                </div>
                            </div>
                        </div>
                        <textarea class="form-control"
                                  id="feedback"
                                  name="feedback"
                                  rows="15">{{ !is_null($tarea->feedback) ? $tarea->feedback : '' }}
                            <p>=== {{ __('Comments').' (v'.($tarea->intentos+1).')' }} ===</p>
                        </textarea>
                        <div class="form-inline mt-3 align-items-right">
                            <label class="me-2">{{ __('Title') }}</label>
                            <input class="form-control me-2" form="guardar_feedback" type="text" id="titulo"
                                   name="titulo">
                            <label class="me-2">{{ __('save as') }}</label>
                            <button form="guardar_feedback" type="submit" name="tipo" value="curso"
                                    class="btn btn-primary">{{ __('course feedback') }}
                            </button>
                            @if(isset($actividad->original))
                                <label class="mx-2">{{ __('or') }}</label>
                                <button form="guardar_feedback" type="submit" name="tipo" value="actividad"
                                        class="btn btn-primary">{{ __('activity feedback') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
            {!! Form::open(['route' => ['feedbacks.save'], 'method' => 'POST', 'id' => 'guardar_feedback']) !!}
            <input form="guardar_feedback" type="hidden" id="mensaje" name="mensaje">
            <input form="guardar_feedback" type="hidden" name="curso_id" value="{{ $actividad->unidad->curso->id }}">
            @if(isset($actividad->original))
                <input form="guardar_feedback" type="hidden" name="actividad_id" value="{{ $actividad->original->id }}">
            @endif
            <input form="guardar_feedback" type="hidden" id="from" name="from" value="tarea">
            {!! Form::close() !!}
            @if($tarea->estado >= 10)
                @include('partials.tarjetas_actividad')
            @endif
            {{-- Fin tarjeta--}}
        </div>
    </div>
</div>
