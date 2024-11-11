@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New role')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('roles.store'))->open() }}

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
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
