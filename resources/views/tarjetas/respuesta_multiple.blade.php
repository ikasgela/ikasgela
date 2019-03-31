@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Tarjeta: Respuesta múltiple</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md">
            <div class="card-columns">

                {{-- Tarjeta --}}
                <div class="card">
                    <div class="card-header">Pregunta rápida</div>
                    <div class="card-body">
                        <h5 class="card-title">Estructuras dinámicas</h5>
                        <p class="card-text">¿Qué estructura de datos mantiene el
                            orden?</p>
                        <form>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="exampleRadios" id="exampleRadios1"
                                           value="option1">
                                    <label class="form-check-label" for="exampleRadios1">
                                        List
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="exampleRadios" id="exampleRadios1"
                                           value="option1">
                                    <label class="form-check-label" for="exampleRadios1">
                                        Set
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                           name="exampleRadios" id="exampleRadios1"
                                           value="option1">
                                    <label class="form-check-label" for="exampleRadios1">
                                        Map
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Responder
                            </button>
                        </form>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-between">
                        <div>10 puntos</div>
                    </div>
                </div>
                {{-- Fin tarjeta--}}

                {{-- Tarjeta --}}
                <div class="card">
                    <div class="card-header">Ponte a prueba</div>
                    <div class="card-body">
                        <h5 class="card-title">Estructuras dinámicas</h5>
                        <p class="card-text">Selecciona aquellas estructuras que NO
                            mantienen el orden.</p>
                        <form>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="exampleRadios" id="exampleRadios1"
                                           value="option1">
                                    <label class="form-check-label" for="exampleRadios1">
                                        List
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="exampleRadios" id="exampleRadios1"
                                           value="option1">
                                    <label class="form-check-label" for="exampleRadios1">
                                        Set
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="exampleRadios" id="exampleRadios1"
                                           value="option1">
                                    <label class="form-check-label" for="exampleRadios1">
                                        Map
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Responder
                            </button>
                        </form>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-between">
                        <div>10 puntos</div>
                    </div>
                </div>
                {{-- Fin tarjeta--}}

            </div>
        </div>
    </div>
@endsection