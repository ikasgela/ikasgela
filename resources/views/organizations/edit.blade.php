@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit organization')])

    <div class="card mb-3">
        <div class="card-body">
            {{ html()->modelForm($organization, 'PUT', route('organizations.update', $organization->id))->open() }}

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])
            @include('components.label-text', [
                'label' => __('Slug'),
                'name' => 'slug',
            ])
            @include('components.label-check', [
                'label' => __('Registration open'),
                'name' => 'registration_open',
            ])
            @include('components.label-text', [
                'label' => __('Available seats'),
                'name' => 'seats',
            ])

            @include('components.label-select', [
                'label' => __('Current period'),
                'name' => 'current_period_id',
                'coleccion' => $organization->periods,
                'opcion' => function ($period) use ($organization) {
                    return html()->option($period->name,
                        $period->id,
                        old('current_period_id', $organization->current_period_id) == $period->id);
                },
                'default' => __('--- None --- '),
            ])

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
