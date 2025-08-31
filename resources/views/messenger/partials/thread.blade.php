@php( $class = !$thread->alert && $thread->isUnread(Auth::id()) ? 'text-bg-success' : '' )

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between {{ $class }} {{ $thread->alert ? 'text-bg-warning' : '' }}">
        <span>
            @if(!$thread->alert)
                <i class="bi bi-chat"></i>
            @else
                <i class="bi bi-exclamation-triangle-fill"></i>
            @endif
            <span class="ms-2">
                {{ $thread->creator()?->name ?: __('Unknown user') }} {{ $thread->creator()?->surname }}
            </span>
        </span>
        <span>{{ $thread->userUnreadMessagesCount(Auth::id()) }} {{ __('unread') }}</span>
    </div>

    @if(config('ikasgela.avatar_enabled'))
        <div class="d-flex align-items-start">
            <div class="ps-3 pt-3">
                @include('users.partials.avatar', ['user' => $thread->creator(), 'width' => 64])
            </div>
            @endif()

            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="card-title"><a
                            href="{{ route('messages.show', $thread->id) }}">{{ $thread->subject }}</a>
                    </h5>
                    @auth
                        @if(Auth::user()->hasRole('profesor'))
                            {{ html()->form('DELETE', route('messages.destroy', $thread->id))->open() }}
                            <div class="btn-group">
                                @include('partials.boton_borrar')
                            </div>
                            {{ html()->form()->close() }}
                        @endif
                    @endauth
                </div>
                @if(!is_null($thread->latestMessage))
                    <small class="text-secondary mb-1">{{ __('Latest message') }}</small>
                    <div class="text-body bg-light-subtle mb-3 line-numbers">
                        <div class="px-3 pt-3 overflow-auto border-start border-secondary-subtle border-4">
                            {!! links_galeria($thread->latestMessage->body, $thread->id) !!}
                        </div>
                    </div>
                    <small class="text-secondary"
                           title="{{ $thread->latestMessage->created_at->isoFormat('dddd, LL LTS') }}">
                        {{ __('Posted') }} {{ $thread->latestMessage->created_at->diffForHumans() }}
                    </small>
                @endif
            </div>

            @if(config('ikasgela.avatar_enabled'))
        </div>
    @endif
</div>
