@include('partials.tutorial', [
    'color' => 'c-callout-success',
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
                    <div class="form-inline">
                        @if($tarea->estado == 11)
                            <button type="submit" name="nuevoestado" value="10"
                                    class="btn btn-secondary single_click">
                                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Show') }}
                            </button>
                        @else
                            <button type="submit" name="nuevoestado" value="31"
                                    class="btn btn-secondary single_click">
                                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Reset') }}
                            </button>
                        @endif
                        <label class="mx-3">{{ __('Attempts') }}: {{ $tarea->intentos }}</label>
                        <div class="form-inline">
                            <button type="submit" name="nuevoestado" value="41"
                                    class="me-3 btn btn-warning single_click"
                                    onclick="return validate_feedback();">
                                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Send again') }}
                            </button>
                            <label class="me-2">{{ __('Score') }}</label>
                            <input class="me-2 form-control" type="number" min="0" max="100" step="1"
                                   name="puntuacion"
                                   value="{{ !is_null($tarea->puntuacion) ? $tarea->puntuacion : $actividad->puntuacion }}"/>
                            <label class="me-3"> {{ __('over') }} {{ $actividad->puntuacion }}</label>
                            <button type="submit" name="nuevoestado" value="40"
                                    class="btn btn-primary me-3 single_click"
                                    onclick="return validate_feedback();">
                                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Finished') }}
                            </button>
                            @if($actividad->is_expired)
                                <button type="submit" name="nuevoestado" value="62"
                                        class="btn btn-secondary ms-3 single_click">
                                    <i class="fas fa-spinner fa-spin"
                                       style="display:none;"></i> {{ __('Archive expired') }}
                                </button>
                                <button type="submit" name="nuevoestado" value="63"
                                        class="btn btn-secondary ms-3 single_click">
                                    <i class="fas fa-spinner fa-spin"
                                       style="display:none;"></i> {{ __('Extend deadline') }}
                                </button>
                                <label class="mx-2">{{ __('by') }}</label>
                                <input class="me-2 form-control" type="number" min="0" max="90" step="1"
                                       name="ampliacion_plazo"
                                       value="{{ $actividad->unidad->curso->plazo_actividad ?? 7 }}"/>
                                <label class="me-2">{{ __('days') }}.</label>
                            @endif
                        </div>
                        @if(!is_null($actividad->siguiente))
                            <div class="form-inline">
                                <label>{{ __('Next') }}: @include('actividades.partials.siguiente')</label>
                                @if($actividad->final)
                                    <button type="submit" name="nuevoestado" value="70"
                                            class="mx-3 btn btn-light single_click">
                                        <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Resume') }}
                                    </button>
                                @else
                                    <button type="submit" name="nuevoestado" value="70"
                                            class="mx-3 btn btn-light single_click">
                                        <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Pause') }}
                                    </button>
                                @endif
                                <button type="submit" name="nuevoestado" value="71"
                                        class="btn btn-light single_click">
                                    <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Show next') }}
                                </button>
                            </div>
                        @endif()
                    </div>
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
