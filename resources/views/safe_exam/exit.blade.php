@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Exiting Safe Exam Browser'), 'subtitulo' => ''])

    <p>{{ __('If you can see this page, something went wrong. Contact your teacher for help.') }}</p>
@endsection
