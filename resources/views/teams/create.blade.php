@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New team')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('teams.store'))->open() }}

            @include('components.label-select', [
                'label' => __('Group'),
                'name' => 'group_id',
                'coleccion' => $groups,
                'opcion' => function ($group) {
                        return html()->option($group->full_name,
                            $group->id,
                            old('group_id', $group->id) == $group->id);
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

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
