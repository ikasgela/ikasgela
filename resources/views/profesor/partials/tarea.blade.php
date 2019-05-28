@if(session('tutorial'))
    <div class="callout callout-success b-t-1 b-r-1 b-b-1">
        <small class="text-muted">{{ __('Tutorial') }}</small>
        <p>Aquí puedes valorar la actividad y dar el feedback oportuno.</p>
    </div>
@endif
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
                    <h2>{{ $actividad->nombre }}</h2>
                    <p>{{ $actividad->descripcion }}</p>
                    <div class="form-inline">
                        <button type="submit" name="nuevoestado" value="31"
                                class="mr-1 btn btn-secondary"> {{ __('Reset') }}
                        </button>
                        <button type="submit" name="nuevoestado" value="41"
                                class="mr-3 btn btn-warning"> {{ __('Send again') }}
                        </button>
                        <label class="mr-2">{{ __('Score') }}</label>
                        <input class="mr-2 form-control" type="number" min="0" max="100" step="1"
                               name="puntuacion"
                               value="{{ !is_null($tarea->puntuacion) ? $tarea->puntuacion : $actividad->puntuacion }}"/>
                        <label class="mr-3"> {{ __('over') }} {{ $actividad->puntuacion }}</label>
                        <button type="submit" name="nuevoestado" value="40"
                                class="btn btn-primary"> {{ __('Finished') }}
                        </button>
                    </div>
                    <div class="mt-2">
                        <i class="fas fa-bullhorn mt-3"></i>
                        <label class="m-0" for="feedback">{{ __('Feedback') }}</label>
                    </div>
                    <div class="border rounded p-3">
                        <div class="form-group d-flex flex-row justify-content-between">
                            {!! Form::label('unidad', __('Message'), ['class' => 'col-form-label']) !!}
                            <div class="flex-fill mx-3">
                                <select class="form-control" id="feedback_id" name="feedback_id">
                                    {{--                                <option value="">{{ __('--- None ---') }}</option>--}}
                                    @foreach($feedbacks as $feedback)
                                        <option value="{{ $feedback->id }}" {{ session('profesor_feedback_actual') == $feedback->id ? 'selected' : '' }}>{{ $feedback->mensaje }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="button" id="boton_feedback"
                                        class="btn btn-primary">{{ __('Add') }}</button>
                            </div>
                        </div>
                        <textarea class="form-control"
                                  id="feedback"
                                  name="feedback"
                                  rows="10">{{ !is_null($tarea->feedback) ? $tarea->feedback : '' }}</textarea>
                    </div>
                </div>
            </form>
            {{-- Fin tarjeta--}}
        </div>
    </div>
    @if($tarea->estado > 10)
        @include('partials.tarjetas_actividad')
    @endif
</div>
