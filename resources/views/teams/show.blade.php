@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline">
        @include('partials.titular', ['titular' => $team->name ])
        <h2 class="text-muted font-xl">{{ $team->group->name ?? '?' }}</h2>
    </div>

    <ul class="list-group mb-4"> @foreach($team->users as $user)
            <li class="list-group-item">
                @include('users.partials.avatar', ['user' => $user, 'width' => 32])
                <span class="mx-2">{{ $user->name }} {{ $user->surname }}</span>
                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
            </li>
        @endforeach
    </ul>

    @include('teams.partials.asignadas', ['disponibles' => $team->actividades()->paginate(config('ikasgela.pagination_assigned_activities'))])

    @include('partials.backbutton')

@endsection
