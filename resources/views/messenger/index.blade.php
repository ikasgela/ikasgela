@extends('layouts.app')

@section('content')
    @include('messenger.partials.flash')

    <a class="btn btn-primary mb-3" href="/messages/create">Create New Message</a>

    @each('messenger.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
@stop
