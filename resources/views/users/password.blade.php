@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Change password')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($user, 'PUT', route('users.update.password', $user->id))->open() }}

            @include('components.label-text-readonly', [
                'label' => __('Username'),
                'name' => 'username',
            ])
            @include('components.label-password', [
                'label' => __('New password'),
                'name' => 'password',
            ])
            @include('components.label-password', [
                'label' => __('Password confirmation'),
                'name' => 'password_confirmation',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
