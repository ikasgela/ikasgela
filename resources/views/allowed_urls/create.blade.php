@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New allowed URL'), 'subtitulo' => ''])

    <form action="{{ route('allowed_urls.store') }}" method="POST">
        @csrf
        <input type="hidden" id="safe_exam_id" name="safe_exam_id" value="{{ $safe_exam->id }}"/>
        <div class="row mb-3">
            <label class="col-2 col-form-label" for="url">{{ __('URL') }}</label>
            <div class="col-10">
                <input class="form-control" type="text" id="url" name="url"
                       value="{{ old('url') }}"/>
                <span class="text-danger">{{ $errors->first('url') }}</span>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-2 form-check-label" for="disabled">{{ __('Disabled') }}</label>
            <div class="col-10">
                <input class="form-check-input" type="checkbox" id="disabled" name="disabled"
                    {{ old('disabled') ? 'checked' : '' }}>
                <span class="text-danger">{{ $errors->first('disabled') }}</span>
            </div>
        </div>
        <div class="mt-5">
            <input class="btn btn-primary text-light" type="submit" name="guardar" value="{{ __('Save') }}"/>
            <a class="btn btn-link text-secondary ms-2"
               href="{{ route('safe_exam.allowed', [$safe_exam->id]) }}">{{ __('Cancel') }}</a>
        </div>
    </form>

@endsection
