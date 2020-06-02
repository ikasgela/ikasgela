<?php
$total = $cuestionario->preguntas()->count();
$correctas = $cuestionario->preguntas()->where('correcta', true)->count();
$respondidas = $cuestionario->preguntas()->where('respondida', true)->count();
$en_blanco = $total - $respondidas;
?>

<div class="card">
    <div class="card-header"><i class="fas fa-question-circle"></i> {{ $cuestionario->titulo }}</div>

    {!! Form::model($cuestionario, ['route' => ['cuestionarios.respuesta', $cuestionario->id], 'method' => 'PUT']) !!}

    @foreach($cuestionario->preguntas as $pregunta)
        <div class="card-body">
            <h5 class="card-title">{{ $pregunta->titulo }}</h5>
            <p class="card-text">{{ $pregunta->texto }}</p>
            <div class="form-group">
                @foreach($pregunta->items as $item)
                    <div class="form-check">
                        <input class="form-check-input
                        {{
                        $pregunta->multiple && $pregunta->respondida && $item->seleccionado ? $item->correcto ? 'is-valid' : 'is-invalid' : ''
                        }}
                        {{
                        $pregunta->multiple && $pregunta->respondida && !$item->seleccionado ? $item->correcto ? 'is-invalid' : 'is-valid' : ''
                        }}
                        {{
                        !$pregunta->multiple && $pregunta->respondida && $item->seleccionado ? $item->correcto ? 'is-valid' : 'is-invalid' : ''
                        }}
                                " type="{{
                        $pregunta->multiple ? 'checkbox' : 'radio'
                        }}"
                               name="respuestas[{{ $pregunta->id }}][]" id="respuestas[{{ $pregunta->id }}]"
                               value="{{ $item->id }}" {{
                               $item->seleccionado ? 'checked' : ''
                               }} {{
                               $cuestionario->respondido ? 'disabled' : ''
                               }}>
                        <label class="form-check-label" for="respuestas[{{ $pregunta->id }}]">
                            {{ $item->texto }}
                        </label>
                        <div class="{{
                        $item->seleccionado && $item->correcto ? 'valid-feedback' : 'invalid-feedback'
                        }}">
                            {{ $item->feedback }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if(!$loop->last)
            <hr class="my-0">
        @endif
    @endforeach
    @if($cuestionario->respondido)
        <div class="card-footer d-flex flex-row justify-content-between">
            <span>{{ __('Right answers') }}: {{ $correctas }} {{ __('of') }} {{ $total }}
                @if($en_blanco > 0)
                    ({{ $en_blanco }} {{ __('without answer') }})
                @endif
            </span>
        </div>
    @elseif(Route::currentRouteName() != 'archivo.show' && Route::currentRouteName() != 'actividades.preview' || !Auth::user()->hasRole('alumno'))
        <hr class="my-0">
        <div class="card-body">
            <button type="submit" class="btn btn-primary single_click">
                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Check answers') }}</button>
        </div>
    @endif

    {!! Form::close() !!}

</div>

@include('layouts.errors')
