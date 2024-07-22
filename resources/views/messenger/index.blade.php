@extends('layouts.app')

@include('partials.prismjs')

@section('fancybox')
    <link rel="stylesheet" href="{{ asset('/js/jquery.fancybox.min.css') }}"/>
    <script src="{{ asset('/js/jquery.fancybox.min.js') }}" defer></script>
@endsection

@php($count = Auth::user()->newThreadsCount())

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Tutorship') }}
                @if(!is_null($curso_actual))
                    <a href="{{ route('messages.create') }}" class="btn btn-primary text-light ms-3">
                        <i class="fas fa-pencil-alt me-1"></i> {{ __('Create new conversation') }}
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

    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-en-curso-tab" data-toggle="tab" href="#pills-en-curso" role="tab"
               aria-controls="pills-profile" aria-selected="true">
                {{ trans_choice('messages.unread', 2) }}
                @if($threads->count() > 0)
                    <span class="ms-2 badge badge-success">{{ $threads->count() }}</span>
                @else
                    <span class="ms-2 badge badge-secondary">0</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-enviadas-tab" data-toggle="tab" href="#pills-enviadas" role="tab"
               aria-controls="pills-contact" aria-selected="false">
                {{ trans_choice('messages.all', 2) }}
                <span class="ms-2 badge badge-secondary">{{ $threads_all_count }}</span>
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
            @include('partials.paginador', ['coleccion' => $threads_all])
        </div>
    </div>
@endsection
