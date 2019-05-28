@extends('layouts.app')

@section('tinymce')
    @include('profesor.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('Assigment review')])

    @include('profesor.partials.tarea')

@endsection
