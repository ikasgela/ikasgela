@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Allowed apps and URLs'), 'subtitulo' => $safe_exam->curso->pretty_name])

    <h2>{{ __('Allowed apps') }}</h2>

    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="thead-dark">
            <tr>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Operating system') }}</th>
                <th>{{ __('Executable') }}</th>
                <th class="text-center">{{ __('Show icon') }}</th>
                <th class="text-center">{{ __('Force close') }}</th>
                <th class="text-center">{{ __('Disabled') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($safe_exam->allowed_apps as $allowed_app)
                <tr>
                    <td>{{ $allowed_app->title }}</td>
                    <td>{{ $allowed_app->os == 1 ? 'Windows' : 'macOS' }}</td>
                    <td>{{ $allowed_app->executable }}</td>
                    <td class="text-center">{{ $allowed_app->show_icon ? __('Yes') : __('No') }}</td>
                    <td class="text-center">{{ $allowed_app->force_close ? __('Yes') : __('No') }}</td>
                    <td class="text-center {{ $allowed_app->disabled ? 'bg-warning text-dark' : '' }}">{{ $allowed_app->disabled ? __('Yes') : __('No') }}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('allowed_apps.edit', [$allowed_app->id]) }}"
                               title="{{ __('Edit allowed app') }}"
                               class="btn btn-sm btn-light me-2" role="button">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('allowed_apps.duplicate', [$allowed_app->id]) }}" method="POST">
                                @csrf
                                <button title="{{ __('Duplicate allowed app') }}"
                                        name="duplicate_allowed_app"
                                        type="submit"
                                        class="btn btn-sm btn-light me-2">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </form>
                            {{ html()->form('DELETE', route('allowed_apps.destroy', $allowed_app->id))->open() }}
                            @include('partials.boton_borrar', ['title' => __('Delete allowed app')])
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-3">
        <a href="{{ route('allowed_apps.create', [$safe_exam->id]) }}"
           class="btn btn-primary" role="button">
            {{ __('New allowed app') }}
        </a>
    </div>

    <h2>{{ __('Allowed URLs') }}</h2>

    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="thead-dark">
            <tr>
                <th>{{ __('URL') }}</th>
                <th class="text-center">{{ __('Disabled') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($safe_exam->allowed_urls as $allowed_url)
                <tr>
                    <td>{{ $allowed_url->url }}</td>
                    <td class="text-center {{ $allowed_url->disabled ? 'bg-warning text-dark' : '' }}">{{ $allowed_url->disabled ? __('Yes') : __('No') }}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('allowed_urls.edit', [$allowed_url->id]) }}"
                               title="{{ __('Edit allowed URL') }}"
                               class="btn btn-sm btn-light me-2" role="button">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('allowed_urls.duplicate', [$allowed_url->id]) }}" method="POST">
                                @csrf
                                <button title="{{ __('Duplicate allowed URL') }}"
                                        name="duplicate_allowed_url"
                                        type="submit"
                                        class="btn btn-sm btn-light me-2">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </form>
                            {{ html()->form('DELETE', route('allowed_urls.destroy', $allowed_url->id))->open() }}
                            @include('partials.boton_borrar', ['title' => __('Delete allowed URL')])
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-3">
        <a href="{{ route('allowed_urls.create', [$safe_exam->id]) }}"
           class="btn btn-primary" role="button">
            {{ __('New allowed URL') }}
        </a>
    </div>

    <div class="mb-3">
        <a class="btn btn-secondary" href="{{ route('safe_exam.index') }}">{{ __('Back') }}</a>
    </div>
@endsection
