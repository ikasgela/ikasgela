@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New period')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('periods.store'))->open() }}

            @include('components.label-select', [
                'label' => __('Organization'),
                'name' => 'organization_id',
                'coleccion' => $organizations,
                'opcion' => function ($organization) {
                        return html()->option($organization->name,
                            $organization->id,
                            old('organization_id') == $organization->id);
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
