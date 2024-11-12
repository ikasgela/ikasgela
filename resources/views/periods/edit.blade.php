@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit period')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($period, 'PUT', route('periods.update', $period->id))->open() }}

            @include('components.label-select', [
                'label' => __('Organization'),
                'name' => 'organization_id',
                'coleccion' => $organizations,
                'opcion' => function ($organization) use ($period) {
                        return html()->option($organization->name,
                            $organization->id,
                            old('organization_id', $period->organization_id) == $organization->id);
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
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
