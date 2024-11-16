@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit allowed app'), 'subtitulo' => ''])

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('allowed_apps.update', [$allowed_app->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <label class="col-2 col-form-label" for="title">{{ __('Title') }}</label>
                    <div class="col-10">
                        <input class="form-control" type="text" id="title" name="title"
                               value="{{ old('title') ?: $allowed_app->title }}"/>
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-2 col-form-label" for="os">{{ __('Operating system') }}</label>
                    <div class="col-10">
                        <select class="form-select" name="os">
                            <option value="1" {{ (old('os') ?: $allowed_app->os) == 1 ? 'selected' : '' }}>Windows
                            </option>
                            <option value="0" {{ (old('os') ?: $allowed_app->os) == 0 ? 'selected' : '' }}>macOS
                            </option>
                        </select>
                        <span class="text-danger">{{ $errors->first('os') }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-2 col-form-label" for="executable">{{ __('Executable') }}</label>
                    <div class="col-10">
                        <input class="form-control" type="text" id="executable" name="executable"
                               value="{{ old('executable') ?: $allowed_app->executable }}"/>
                        <span class="text-danger">{{ $errors->first('executable') }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-2 col-form-label" for="path">{{ __('Path') }}</label>
                    <div class="col-10">
                        <input class="form-control" type="text" id="path" name="path"
                               value="{{ old('path') ?: $allowed_app->path }}"/>
                        <span class="text-danger">{{ $errors->first('path') }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-2 col-form-label" for="identifier">{{ __('Identifier') }}</label>
                    <div class="col-10">
                        <input class="form-control" type="text" id="identifier" name="identifier"
                               value="{{ old('identifier') ?: $allowed_app->identifier }}"/>
                        <span class="text-danger">{{ $errors->first('identifier') }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-2 form-check-label" for="show_icon">{{ __('Show icon') }}</label>
                    <div class="col-10">
                        <input type="hidden" name="show_icon-isset" value="1">
                        <input class="form-check-input" type="checkbox" id="show_icon" name="show_icon" value="1"
                            {{ old('show_icon-isset') ? (old('show_icon') ? 'checked' : '') : ($allowed_app->show_icon ? 'checked' : '') }}>
                        <span class="text-danger">{{ $errors->first('show_icon') }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-2 form-check-label" for="force_close">{{ __('Force close') }}</label>
                    <div class="col-10">
                        <input type="hidden" name="force_close-isset" value="1">
                        <input class="form-check-input" type="checkbox" id="force_close" name="force_close" value="1"
                            {{ old('force_close-isset') ? (old('force_close') ? 'checked' : '') : ($allowed_app->force_close ? 'checked' : '') }}>
                        <span class="text-danger">{{ $errors->first('force_close') }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-2 form-check-label" for="disabled">{{ __('Disabled') }}</label>
                    <div class="col-10">
                        <input type="hidden" name="disabled-isset" value="1">
                        <input class="form-check-input" type="checkbox" id="disabled" name="disabled" value="1"
                            {{ old('disabled-isset') ? (old('disabled') ? 'checked' : '') : ($allowed_app->disabled ? 'checked' : '') }}>
                        <span class="text-danger">{{ $errors->first('disabled') }}</span>
                    </div>
                </div>
                <div>
                    <input class="btn btn-primary" type="submit" name="guardar" value="{{ __('Save') }}"/>
                    <a class="btn btn-link text-secondary ms-2"
                       href="{{ route('safe_exam.allowed', [$allowed_app->safe_exam->id]) }}">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
