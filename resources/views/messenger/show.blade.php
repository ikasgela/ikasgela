@extends('layouts.app')

@section('tinymce')
    @include('messenger.partials.tinymce')
@endsection

@section('content')
    <h1>{{ $thread->subject }}</h1>
    <div>
        @each('messenger.partials.messages', $thread->messages, 'message')
        @include('messenger.partials.form-message')
    </div>
@stop
