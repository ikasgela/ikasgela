@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Messages')])

    @include('messenger.partials.flash')

    <a class="btn btn-primary mb-3" href="/messages/create">{{ __('Create new conversation') }}</a>

    @each('messenger.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
@stop
