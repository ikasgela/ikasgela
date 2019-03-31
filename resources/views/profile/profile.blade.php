@extends('layouts.app')

@section('header')
    @include('layouts.header')
@endsection

@section('sidebar')
    @include('layouts.sidebar')
@endsection

@section('content')
    <div class="row">
        <div class="col-md">
            <h1>{{ __('Profile') }}</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="py-4">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <profile></profile>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
