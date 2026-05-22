@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Import :name', ['name' => __('Questionnaire')])])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('cuestionarios.importar'))->attribute('enctype', 'multipart/form-data')->open() }}

            <div class="mb-3">
                <label for="fichero" class="form-label">{{ __('JSON file') }}</label>
                <input type="file" class="form-control @error('fichero') is-invalid @enderror"
                       id="fichero" name="fichero" accept=".json">
                @error('fichero')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
