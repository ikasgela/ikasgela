@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => $team->group->name . $team->name ])

    <div>
        <p>Miembros</p>
        @forelse($team->users as $user)
            <p class="m-0">{{ $user->name }} {{ $user->surname }}</p>
        @empty
            <p class="m-0">{{ trans_choice('genero.none', 1) }}</p>
        @endforelse
    </div>

    <div>
        <p>Tareas asignadas</p>
    </div>

    @include('partials.backbutton')

@endsection
