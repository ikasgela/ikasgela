@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Nueva unidad</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('unidades.store') }}">
                @csrf

                <div class="form-group">
                    <label for="curso_id">Curso:</label>
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre">
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('unidades.index') }}" class="btn btn-link text-secondary">Cancelar</a>
                </div>

                @include('layouts.errors')
            </form>

        </div>
    </div>
@endsection
