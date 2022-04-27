@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => $team->name, 'subtitulo' => $team->group->pretty_name ])

    <ul class="list-group mb-4"> @foreach($team->users as $user)
            <li class="list-group-item">
                @include('users.partials.avatar', ['user' => $user, 'width' => 32])
                <span class="mx-2">{{ $user->name }} {{ $user->surname }}</span>
                @include('partials.mailto', ['user' => $user])
            </li>
        @endforeach
    </ul>

    @include('teams.partials.asignadas', ['disponibles' => $team->actividades()->paginate(config('ikasgela.pagination_assigned_activities'))])

    @include('partials.backbutton')

@endsection
