@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('YouTube video')])

    <div class="row">
        <div class="col-md-6">
            @include('youtube_videos.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection
