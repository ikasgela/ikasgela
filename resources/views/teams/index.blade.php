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
                    <td class="p-0 ps-1"></td>
                    <td>
                        <input form="asignar" type="checkbox"
                               data-chkbox-shiftsel="grupo4"
                               name="equipos_seleccionados[{{ $team->id }}]" value="{{ $team->id }}">
                    </td>
                    <td class="clickable">{{ $team->group->full_name }}</td>
                    <td class="clickable">{{ $team->name }}</td>
                    <td class="clickable">{{ $team->group->slug }}/{{ $team->slug }}</td>
                    <td class="clickable">
                        @forelse($team->users as $user)
                            <p class="m-0">{{ $user->full_name }}</p>
                        @empty
                            <p class="m-0">{{ trans_choice('genero.none', 1) }}</p>
                        @endforelse
                    </td>
                    <td class="clickable text-center">{{ $team->actividades->count() }}</td>
                    <td>
                        <div class='btn-group'>
                            <a title="{{ __('Preview') }}"
                               href="{{ route('teams.show', [$team->id]) }}"
                               class='btn btn-light btn-sm'><i class="bi bi-eye"></i></a>

                            @if(Auth::user()?->hasAnyRole(['profesor', 'admin']))
                                {{ html()->form('POST', route('messages.create-with-subject-team'))->open() }}
                                {{ html()->submit('<i class="fas fa-envelope"></i>')
                                        ->class(['btn btn-light btn-sm', 'rounded-0'])
                                        ->attribute('title', __('Message')) }}
                                {{ html()->hidden('team_id', $team->id) }}
                                {{ html()->form()->close() }}
                            @endif

                            @if(Auth::user()?->hasRole('admin'))
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('teams.edit', [$team->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>

                                {{ html()->form('DELETE', route('teams.destroy', $team->id))->open() }}
                                @include('partials.boton_borrar', ['last' => true])
                                {{ html()->form()->close() }}
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
