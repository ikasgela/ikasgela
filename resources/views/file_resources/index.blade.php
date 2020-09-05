@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: File resources')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('file_resources.create') }}">{{ __('New file resource') }}</a>
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
            @foreach($file_resources as $file_resource)
                <tr>
                    <td>{{ $file_resource->id }}</td>
                    <td>{{ $file_resource->titulo }}</td>
                    <td>{{ $file_resource->descripcion }}</td>
                    <td>{{ $file_resource->max_files }}</td>
                    <td>{!! $file_resource->plantilla ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-nowrap">
                        {!! Form::open(['route' => ['file_resources.destroy', $file_resource->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Show') }}"
                               href="{{ route('file_resources.show', [$file_resource->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('file_resources.edit', [$file_resource->id]) }}"
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
