@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit allowed URL'), 'subtitulo' => ''])

    <form action="{{ route('allowed_urls.update', [$allowed_url->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <label class="col-2 col-form-label" for="url">{{ __('URL') }}</label>
            <div class="col-10">
                <input class="form-control" type="text" id="url" name="url"
                       value="{{ old('url') ?: $allowed_url->url }}"
                       placeholder="https://wikipedia.org"/>
                <span class="text-danger">{{ $errors->first('url') }}</span>
            </div>
        </div>
        <div class="mt-5">
            <input class="btn btn-primary" type="submit" name="guardar" value="{{ __('Save') }}"/>
            <a class="btn btn-link text-secondary ms-2"
               href="{{ route('safe_exam.allowed', [$allowed_url->safe_exam->id]) }}">{{ __('Cancel') }}</a>
        </div>
    </form>

@endsection
