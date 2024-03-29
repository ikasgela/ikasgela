@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Qualifications')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['qualifications.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('qualifications.create') }}">{{ __('New qualification') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($qualifications as $qualification)
                <tr>
                    <td>{{ $qualification->id }}</td>
                    <td>{{ $qualification->curso->full_name }}</td>
                    <td>{{ $qualification->name }}</td>
                    <td>{{ $qualification->description }}</td>
                    <td class="text-nowrap">
                        {!! Form::open(['route' => ['qualifications.destroy', $qualification->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('qualifications.edit', [$qualification->id]) }}"
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
