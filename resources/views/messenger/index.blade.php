@extends('layouts.app')

@include('partials.prismjs')

@section('fancybox')
    <link rel="stylesheet" href="{{ asset('build/js/jquery.fancybox.min.css') }}"/>
    <script src="{{ asset('build/js/jquery.fancybox.min.js') }}" defer></script>
@endsection

@php($count = Auth::user()->newThreadsCount())

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Tutorship') }}
                @if(!is_null($curso_actual))
                    <a href="{{ route('messages.create') }}" class="btn btn-primary ms-3">
                        <i class="bi bi-chat-text me-1"></i> {{ __('Create new conversation') }}
                    </a>
                @endif
            </h1>
        </div>
        <div>
            @if($count > 0)
                @if($count == 1)
                    <h2 class="text-muted font-xl">{{ __('One new message') }}</h2>
                @else
                    <h2 class="text-muted font-xl">{{ __(':count new messages', ['count' => $count]) }}</h2>
                @endif
            @else
                <h2 class="text-muted font-xl">{{ __('There are no new messages') }}</h2>
            @endif
        </div>
    </div>

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.mensajes')
    ])

    @include('messenger.partials.flash')

    <ul class="nav nav-tabs mb-3" id="tab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active d-flex align-items-center"
                    id="no-leidas-tab" data-bs-target="#no-leidas-tab-pane" aria-controls="no-leidas-tab-pane"
                    data-bs-toggle="tab" type="button" role="tab"
                    aria-selected="true">
                <span>{{ trans_choice('messages.unread', 2) }}</span>
                @if($threads->count() > 0)
                    <span class="ms-2 badge text-bg-success fw-light">{{ $threads->count() }}</span>
                @else
                    <span class="ms-2 badge text-bg-secondary fw-light">0</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link d-flex align-items-center"
                    id="todas-tab" data-bs-target="#todas-tab-pane" aria-controls="todas-tab-pane"
                    data-bs-toggle="tab" type="button" role="tab"
                    aria-selected="false">
                <span>{{ trans_choice('messages.all', 2) }}</span>
                <span class="ms-2 badge text-bg-secondary fw-light">{{ $threads_all_count }}</span>
            </button>
        </li>
    </ul>
    <div class="tab-content" id="tab-content">
        <div class="tab-pane fade show active"
             id="no-leidas-tab-pane" aria-labelledby="no-leidas-tab"
             role="tabpanel">
            @each('messenger.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
        </div>
        <div class="tab-pane fade"
             id="todas-tab-pane" aria-labelledby="todas-tab"
             role="tabpanel">
            @each('messenger.partials.thread', $threads_all, 'thread', 'messenger.partials.no-threads')
            @include('partials.paginador', ['coleccion' => $threads_all])
        </div>
    </div>
@endsection
