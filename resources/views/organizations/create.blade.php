@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New organization')])

    <div class="card mb-3">
        <div class="card-body">
            {{ html()->form('POST', route('organizations.store'))->open() }}

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

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
