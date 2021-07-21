@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Users') }}</h1>
        <div class="form-inline">
            <div class="btn-toolbar" role="toolbar">
                {!! Form::open(['route' => ['users.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Clear filters'), ['type' => 'submit',
                    'class' => session('profesor_filtro_etiquetas') == 'S' ? 'btn btn-sm mx-1 btn-primary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_etiquetas','N') !!}
                {!! Form::close() !!}
            </div>
        </div>
        <div></div>
    </div>

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['users.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_organizations')
        {!! Form::close() !!}
    @endif

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
                    <td>@include('users.partials.avatar', ['user' => $user, 'width' => 35])</td>
                    <td>
                        {{ $user->name }} {{ $user->surname }}
                        @include('profesor.partials.status_usuario_filtro')
                    </td>
                    <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                    <td class="text-center">{!! $user->hasVerifiedEmail() ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td class="text-center">{!! $user->tutorial ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                    <td>
                        @foreach($user->roles as $rol)
                            {{ !$loop->last ? $rol->name . ', ' : $rol->name }}
                        @endforeach
                    </td>
                    <td>
                        @include('users.partials.acciones')
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
