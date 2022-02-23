@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Milestones')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('milestones.create') }}">{{ __('New milestone') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Date') }}</th>
                <th class="text-center">{{ __('Published') }}</th>
                <th class="text-center">{{ __('Decimals') }}</th>
                <th class="text-center">{{ __('Truncate') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($milestones as $milestone)
                <tr>
                    <td>{{ $milestone->id }}</td>
                    <td>{{ $milestone->curso->full_name }}</td>
                    <td>{{ $milestone->name }}</td>
                    <td>{{ $milestone->date }}</td>
                    <td class="text-center">@include('partials.check_yes_no', ['checked' => $milestone->published])</td>
                    <td class="text-center">{{ $milestone->decimals }}</td>
                    <td class="text-center">@include('partials.check_yes_no', ['checked' => $milestone->truncate])</td>
                    <td class="text-nowrap">
                        {!! Form::open(['route' => ['milestones.destroy', $milestone->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('milestones.edit', [$milestone->id]) }}"
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
