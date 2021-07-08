@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Categories')])

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
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('categories.edit', [$category->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>

                            {!! Form::open(['route' => ['categories.destroy', $category->id], 'method' => 'DELETE']) !!}
                            @include('partials.boton_borrar')
                            {!! Form::close() !!}
                        </div>
                    </td>
                    <td>
                        {!! Form::open(['route' => ['cursos.import'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                        <div class="form-group form-inline">
                            <span class="d-flex flex-column">
                                <input type="file" name="file" id="file">
                                <span class="help-block text-danger">{{ $errors->first('file') }}</span>
                            </span>
                            {!! Form::button('<i class="fas fa-spinner fa-spin" style="display:none;"></i>' . __('Import course'),
                                ['type' => 'submit', 'class' => 'btn btn-primary single_click' ]) !!}
                        </div>
                        {!! Form::hidden('category_id', $category->id) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
