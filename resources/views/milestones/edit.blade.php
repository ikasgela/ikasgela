@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit milestone')])

    <div class="card mb-3">
        <div class="card-body">

            @livewire('milestone-form', ['milestone' => $milestone])

        </div>
    </div>

@endsection
