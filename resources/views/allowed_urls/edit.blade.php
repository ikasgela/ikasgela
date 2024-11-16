@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit allowed URL'), 'subtitulo' => ''])

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('allowed_urls.update', [$allowed_url->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <label class="col-2 col-form-label" for="url">{{ __('URL') }}</label>
                    <div class="col-10">
                        <input class="form-control" type="text" id="url" name="url"
                               value="{{ old('url') ?: $allowed_url->url }}"/>
                        <span class="text-danger">{{ $errors->first('url') }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-2 form-check-label" for="disabled">{{ __('Disabled') }}</label>
                    <div class="col-10">
                        <input type="hidden" name="disabled-isset" value="1">
                        <input class="form-check-input" type="checkbox" id="disabled" name="disabled" value="1"
                            {{ old('disabled-isset') ? (old('disabled') ? 'checked' : '') : ($allowed_url->disabled ? 'checked' : '') }}>
                        <span class="text-danger">{{ $errors->first('disabled') }}</span>
                    </div>
                </div>
                <div>
                    <input class="btn btn-primary" type="submit" name="guardar" value="{{ __('Save') }}"/>
                    <a class="btn btn-link text-secondary ms-2"
                       href="{{ route('safe_exam.allowed', [$allowed_url->safe_exam->id]) }}">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
