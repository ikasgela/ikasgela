@extends('layouts.app')

@php($count = Auth::user()->newThreadsCount())

@section('content')

    <div class="d-flex flex-row justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Messages') }}</h1>
        </div>
        <div>
            @if($count > 0)
                @if($count == 1)
                    <h2 class="text-muted font-xl">Un mensaje nuevo</h2>
                @else
                    <h2 class="text-muted font-xl">{{ $count }} mensajes nuevos</h2>
                @endif
            @else
                <h2 class="text-muted font-xl">No hay mensajes nuevos</h2>
            @endif
        </div>
    </div>

    @include('messenger.partials.flash')

    <a class="btn btn-primary mb-3" href="/messages/create">{{ __('Create new conversation') }}</a>

    @each('messenger.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
@stop
