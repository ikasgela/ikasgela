@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Teams')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('teams.create') }}">{{ __('New team') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Group') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($teams as $team)
                <tr>
                    <td>{{ $team->id }}</td>
                    <td>{{ $team->group->name }}</td>
                    <td>{{ $team->name }}</td>
                    <td>{{ $team->slug }}</td>
                    <td>
                        {!! Form::open(['route' => ['teams.destroy', $team->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('teams.edit', [$team->id]) }}"
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
