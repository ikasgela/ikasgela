@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Editar unidad</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('unidades.update', [$unidad->id]) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="curso_id">Curso:</label>
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" <?php if ($unidad->curso_id == $curso->id) echo 'selected'; ?>>{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $unidad->nombre }}">
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion"
                           value="{{ $unidad->descripcion }}">
                </div>
                <div class="form-group">
                    <label for="slug">Slug:</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="{{ $unidad->slug }}">
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
