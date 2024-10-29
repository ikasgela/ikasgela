@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <p class="alert alert-danger">{{ __('Account blocked, contact your administrator.') }}</p>
                @include('auth.partials.back-homepage')
            </div>
        </div>
    </div>
@endsection
