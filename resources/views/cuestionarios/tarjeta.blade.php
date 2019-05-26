<div class="card">
    <div class="card-header"><i class="fas fa-question-circle"></i> {{ __('Questionnaire') }}</div>
    <div class="card-body">
        <h5 class="card-title">¿Correcto?</h5>
        <p class="card-text">Un bucle while puede no ejecutarse
            nunca.</p>
        <form>
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input is-valid" type="radio"
                           name="exampleRadios" id="exampleRadios1"
                           value="option1" disabled checked>
                    <label class="form-check-label" for="exampleRadios1">
                        Sí
                    </label>
                    <div class="valid-feedback">
                        Correcto, es un bucle de 0 a N.
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="exampleRadios" id="exampleRadios1"
                           value="option1" disabled>
                    <label class="form-check-label" for="exampleRadios1">
                        No
                    </label>
                    <div class="invalid-feedback">
                        El bucle while es un bucle de 0 a N. La condición
                        se evalua antes de entrar en el bucle y puede que
                        nunca lleguen a ejecutarse las instrucciones del
                        cuerpo del bucle si la primera vez se evalua a
                        false.
                    </div>
                </div>
            </div>
        </form>
        <p class="card-text text-right">
            <small class="text-muted">Respondida el 24/02/2019 a las 12:34
                CET
            </small>
        </p>
        <form>
            <button type="submit" class="btn btn-secondary mb-2">Archivar
            </button>
        </form>
    </div>
    <div class="card-footer d-flex flex-row justify-content-between">
        <div>10 puntos</div>
    </div>
</div>
