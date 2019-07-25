@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('File upload')])

    <div class="row">
        <div class="col-md-6">
            @include('file_uploads.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection