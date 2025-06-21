@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Categories'), 'subtitulo' => ''])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('categories.create') }}">{{ __('New category') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Organization') }}</th>
                <th>{{ __('Period') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th colspan="2">{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->period->organization->name }}</td>
                    <td>{{ $category->period->name }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>
                        {{ html()->form('DELETE', route('categories.destroy', $category->id))->open() }}
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('categories.edit', [$category->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                            @include('partials.boton_borrar')
                        </div>
                        {{ html()->form()->close() }}
                    </td>
                    <td>
                        {{ html()->form('POST', route('cursos.import'))->acceptsFiles()->open() }}
                        <div class="d-flex flex-column">
                            <div class="row gx-0">
                                <div class="col-auto">
                                    {{ html()->file('file')->class("form-control") }}
                                </div>
                                <div class="col">
                                    {{ html()->submit('<i class="fas fa-spinner fa-spin" style="display:none;"></i> ' . __('Import course'))
                                                ->class('btn btn-primary single_click ms-3') }}
                                </div>
                            </div>
                            <div class="help-block small text-danger">{{ $errors->first('file') }}</div>
                        </div>
                        {{ html()->hidden('category_id', $category->id) }}
                        {{ html()->form()->close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
