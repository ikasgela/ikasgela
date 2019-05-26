<div class="card">
    <div class="card-header"><i class="fas fa-question-circle"></i> {{ $cuestionario->titulo }}</div>

    @foreach($cuestionario->preguntas as $pregunta)
        <div class="card-body">
            <h5 class="card-title">{{ $pregunta->titulo }}</h5>
            <p class="card-text">{{ $pregunta->texto }}</p>
            <form>
                <div class="form-group">
                    @foreach($pregunta->items as $item)
                        <div class="form-check">
                            <input class="form-check-input {{
                        $item->seleccionado && $item->correcto ? 'is-valid' : ''
                        }}{{
                        $item->seleccionado && !$item->correcto ? 'is-invalid' : ''
                        }}" type="{{
                        $pregunta->multiple ? 'checkbox' : 'radio'
                        }}"
                                   name="pregunta_{{ $pregunta->id }}" id="pregunta_{{ $pregunta->id }}"
                                   value="item_{{ $item->id }}" {{
                               $item->seleccionado ? 'checked' : ''
                               }}{{
                               $pregunta->respondida ? 'disabled' : ''
                               }}>
                            <label class="form-check-label" for="pregunta_{{ $pregunta->id }}">
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
            </form>
        </div>
        <hr class="my-0">
    @endforeach
    <div class="card-body">
        <form>
            <button type="submit" class="btn btn-primary">{{ __('Answer') }}</button>
        </form>
    </div>
</div>
