@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Links')])

    <div class="row">
        <div class="col-md-6">
            @include('link_collections.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection
