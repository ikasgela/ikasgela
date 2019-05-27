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
                               name="preguntas[{{ $pregunta->id }}][]" id="preguntas[{{ $pregunta->id }}]"
                               value="{{ $item->id }}" {{
                               $item->seleccionado ? 'checked' : ''
                               }} {{
                               $pregunta->respondida ? 'disabled' : ''
                               }}>
                        <label class="form-check-label" for="preguntas[{{ $pregunta->id }}]">
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
        <hr class="my-0">
    @endforeach
    <div class="card-body">
        <button type="submit" class="btn btn-primary">{{ __('Answer') }}</button>
    </div>

    {!! Form::close() !!}

</div>
