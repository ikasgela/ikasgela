@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Flashcards')])

    <div class="row">
        <div class="col-md-12">
            <livewire:flash-deck-component :$flash_deck/>
        </div>
    </div>

    @include('partials.backbutton')

@endsection
