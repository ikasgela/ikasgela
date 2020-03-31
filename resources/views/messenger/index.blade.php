@extends('layouts.app')

@include('partials.prismjs')

@php($count = Auth::user()->newThreadsCount())

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Tutorship') }}
                <a href="{{ route('messages.create') }}" class="btn btn-primary ml-3">
                    <i class="fas fa-pencil-alt mr-1"></i> {{ __('Create new conversation') }}
                </a>
            </h1>
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

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Aquí puedes iniciar conversaciones para ayudarte a resolver tus dudas sobre las actividades.'
    ])

    @include('messenger.partials.flash')

    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-en-curso-tab" data-toggle="tab" href="#pills-en-curso" role="tab"
               aria-controls="pills-profile" aria-selected="true">
                {{ trans_choice('messages.unread', 2) }}
                @if($threads->count() > 0)
                    <span class="ml-2 badge badge-success">{{ $threads->count() }}</span>
                @else
                    <span class="ml-2 badge badge-secondary">0</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-enviadas-tab" data-toggle="tab" href="#pills-enviadas" role="tab"
               aria-controls="pills-contact" aria-selected="false">
                {{ trans_choice('messages.all', 2) }}
                <span class="ml-2 badge badge-secondary">{{ $threads_all->count() }}</span>
            </a>
        </li>
    </ul>
    <div class="tab-content border-bottom border-left border-right" id="pills-tab-content">
        <div class="tab-pane fade show active" id="pills-en-curso" role="tabpanel" aria-labelledby="pills-en-curso-tab">
            <div class="p-3">
                @each('messenger.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
            </div>
        </div>
        <div class="tab-pane fade" id="pills-enviadas" role="tabpanel" aria-labelledby="pills-enviadas-tab">
            <div class="p-3">
                @each('messenger.partials.thread', $threads_all, 'thread', 'messenger.partials.no-threads')
            </div>
        </div>
    </div>
@stop
