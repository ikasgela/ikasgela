@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Tarjeta: Respuesta corta</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md">
            <div class="card-columns">

                {{-- Tarjeta --}}
                <div class="card">
                    <div class="card-header">Antes de continuar...</div>
                    <div class="card-body">
                        <h5 class="card-title">Expresiones lambda</h5>
                        <p class="card-text">Explica brévemente en qué consiste el
                            concepto de captura de variables por parte de una expresión
                            lambda.</p>
                        <form>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Tu
                                    respuesta:</label>
                                <textarea class="form-control"
                                          id="exampleFormControlTextarea1"
                                          rows="3"></textarea>
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
                    <div class="card-header">Antes de continuar...</div>
                    <div class="card-body">
                        <h5 class="card-title">Expresiones lambda</h5>
                        <p class="card-text">Explica brévemente en qué consiste el
                            concepto de captura de variables por parte de una expresión
                            lambda.</p>
                    </div>
                    <hr class="mt-0 mb-2">
                    <div class="card-body">
                        <h6 class="card-subtitle text-muted mb-2">Tu respuesta</h6>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                            exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</p>
                        <p class="card-text text-right">
                            <small class="text-muted">Enviada el 24/02/2019 a las 12:34
                                CET
                            </small>
                        </p>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-between">
                        <div>10 puntos</div>
                    </div>
                </div>
                {{-- Fin tarjeta--}}

                {{-- Tarjeta --}}
                <div class="card">
                    <div class="card-header">Antes de continuar...</div>
                    <div class="card-body">
                        <h5 class="card-title">Expresiones lambda</h5>
                        <p class="card-text">Explica brévemente en qué consiste el
                            concepto de captura de variables por parte de una expresión
                            lambda.</p>
                    </div>
                    <hr class="mt-0 mb-2">
                    <div class="card-body">
                        <h6 class="card-subtitle text-muted mb-2">Tu respuesta</h6>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                            exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</p>
                        <p class="card-text text-right">
                            <small class="text-muted">Enviada el 24/02/2019 a las 12:34
                                CET
                            </small>
                        </p>
                    </div>
                    <hr class="mt-0 mb-2">
                    <div class="card-body">
                        <h6 class="card-subtitle text-muted mb-2">Feedback</h6>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                            exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.</p>
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
                    <hr class="mt-0 mb-2">
                    <div class="card-body">
                        <h6 class="card-subtitle text-muted mb-2">Actividades
                            recomendadas</h6>
                        <a href="#">Expresiones lambda</a><br>
                        <a href="#">Interfaces funcionales</a>
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