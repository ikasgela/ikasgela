@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Users')])

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th></th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th class="text-center">{{ __('Verified') }}</th>
                <th class="text-center">{{ __('Tutorial') }}</th>
                <th>{{ __('Roles') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td><img style="height:35px;" src="{{ $user->avatar_url(70) }}"/></td>
                    <td>{{ $user->name }}</td>
                    <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                    <td class="text-center">{!! $user->email_verified_at ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-center">{!! $user->tutorial ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td>
                        @foreach($user->roles as $rol)
                            {{ !$loop->last ? $rol->name . ', ' : $rol->name }}
                        @endforeach
                    </td>
                    <td>
                        <form method="POST" action="{{ route('users.destroy', [$user->id]) }}">
                            @csrf
                            @method('DELETE')
                            <div class='btn-group'>
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('users.edit', [$user->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                @include('partials.boton_borrar')
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
