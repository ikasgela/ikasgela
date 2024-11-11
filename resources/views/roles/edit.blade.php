@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit role')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($role, 'PUT', route('roles.update', $role->id))->open() }}

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'description',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
