@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Markdown text')])

    <div class="row">
        <div class="col-md-6">
            @include('markdown_texts.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection