@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit team')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($team, 'PUT', route('teams.update', $team->id))->open() }}

            @include('components.label-select', [
                'label' => __('Group'),
                'name' => 'group_id',
                'coleccion' => $groups,
                'opcion' => function ($group) use ($team) {
                        return html()->option($group->full_name,
                            $group->id,
                            old('group_id', $team->group_id) == $group->id);
                },
            ])

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])
            @include('components.label-text', [
                'label' => __('Slug'),
                'name' => 'slug',
            ])

            @include('teams.partials.selector_equipos')

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
