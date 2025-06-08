@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('IntelliJ project')])

    <div class="row">
        <div class="col-md-6">
            @livewire('tarjeta-intellij', ['actividad' => null, 'intellij_project' => $intellij_project])
        </div>
    </div>

    @include('partials.backbutton')

@endsection
