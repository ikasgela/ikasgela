@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Periods')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('periods.create') }}">{{ __('New period') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Organization') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($periods as $period)
                <tr>
                    <td>{{ $period->id }}</td>
                    <td>{{ $period->organization->name }}</td>
                    <td>{{ $period->name }}</td>
                    <td>{{ $period->slug }}</td>
                    <td>
                        {!! Form::open(['route' => ['periods.destroy', $period->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('periods.edit', [$period->id]) }}"
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
