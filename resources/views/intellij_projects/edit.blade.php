@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Editar repositorio de IntelliJ</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('intellij_projects.update', [$intellij_project->id]) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="repositorio">Repositorio</label>
                    <input type="text" class="form-control" id="repositorio" name="repositorio"
                           value="{{ $intellij_project->repositorio }}">
                </div>
                <div class=" form-group">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('intellij_projects.index') }}" class="btn btn-link text-secondary">Cancelar</a>
                </div>

                @include('layouts.errors')
            </form>

        </div>
    </div>
@endsection
