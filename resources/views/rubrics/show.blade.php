@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Rubric')])

    <div class="row">
        <div class="col-md-12">
            @livewire('rubric-show', ['rubric' => $rubric])
        </div>
    </div>

    @include('partials.backbutton')

@endsection
