@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit group')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($group, 'PUT', route('groups.update', $group->id))->open() }}

            @include('components.label-select', [
                'label' => __('Period'),
                'name' => 'period_id',
                'coleccion' => $periods,
                'opcion' => function ($period) use ($group) {
                        return html()->option($period->full_name,
                            $period->id,
                            old('period_id', $group->period_id) == $period->id);
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

            @include('users.partials.selector_cursos')

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
