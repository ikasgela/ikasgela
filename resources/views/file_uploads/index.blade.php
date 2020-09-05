@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: Image uploads')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('file_uploads.create') }}">{{ __('New image upload') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Maximum') }}</th>
                <th>{{ __('Template') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($file_uploads as $file_upload)
                <tr>
                    <td>{{ $file_upload->id }}</td>
                    <td>{{ $file_upload->titulo }}</td>
                    <td>{{ $file_upload->descripcion }}</td>
                    <td>{{ $file_upload->max_files }}</td>
                    <td>{!! $file_upload->plantilla ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-nowrap">
                        {!! Form::open(['route' => ['file_uploads.destroy', $file_upload->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Show') }}"
                               href="{{ route('file_uploads.show', [$file_upload->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('file_uploads.edit', [$file_upload->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                            @include('partials.boton_borrar')
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
