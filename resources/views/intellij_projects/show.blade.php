@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('IntelliJ project')])

    <div class="row">
        <div class="col-md-6">
            @include('intellij_projects.tarjeta')
        </div>
    </div>

    @include('partials.backbutton')

@endsection
