@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Teams')])

    @if(Auth::user()?->hasRole('admin'))
        <div class="mb-3">
            <a class="btn btn-primary" href="{{ route('teams.create') }}">{{ __('New team') }}</a>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th class="p-0"></th>
                <th>
                    <input type="checkbox" id="seleccionar_equipos">
                </th>
                <th>{{ __('Group') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Members') }}</th>
                <th class="text-center">{{ __('Assigned activities') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($teams as $team)
                <tr class="table-cell-click" data-href="{{ route('teams.show', [$team->id]) }}">
                    <td class="p-0 pl-1"></td>
                    <td>
                        <input form="asignar" type="checkbox"
                               name="equipos_seleccionados[{{ $team->id }}]" value="{{ $team->id }}">
                    </td>
                    <td class="clickable">{{ $team->group->full_name }}</td>
                    <td class="clickable">{{ $team->name }}</td>
                    <td class="clickable">{{ $team->group->slug }}/{{ $team->slug }}</td>
                    <td class="clickable">
                        @forelse($team->users as $user)
                            <p class="m-0">{{ $user->name }} {{ $user->surname }}</p>
                        @empty
                            <p class="m-0">{{ trans_choice('genero.none', 1) }}</p>
                        @endforelse
                    </td>
                    <td class="clickable text-center">{{ $team->actividades->count() }}</td>
                    <td>
                        <div class='btn-group'>
                            <a title="{{ __('Preview') }}"
                               href="{{ route('teams.show', [$team->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-eye"></i></a>

                            @if(Auth::user()?->hasAnyRole(['profesor', 'admin']))
                                {!! Form::open(['route' => ['messages.create-with-subject-team'], 'method' => 'POST']) !!}
                                {!! Form::button('<i class="fas fa-envelope"></i>', ['type' => 'submit',
                                    'class' => 'btn btn-light btn-sm', 'title' => __('Message')
                                ]) !!}
                                {!! Form::hidden('team_id', $team->id) !!}
                                {!! Form::close() !!}
                            @endif

                            @if(Auth::user()?->hasRole('admin'))
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('teams.edit', [$team->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>

                                {!! Form::open(['route' => ['teams.destroy', $team->id], 'method' => 'DELETE']) !!}
                                @include('partials.boton_borrar')
                                {!! Form::close() !!}
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @include('teams.partials.disponibles_grupo')

@endsection
