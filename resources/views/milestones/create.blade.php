@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New milestone')])

    <div class="card mb-3">
        <div class="card-body">

            @livewire('milestone-form', ['curso_id' => $curso_actual?->id])

        </div>
    </div>

@endsection
