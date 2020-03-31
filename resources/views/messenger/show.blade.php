@extends('layouts.app')

@include('partials.prismjs')

@section('tinymce')
    @include('messenger.partials.tinymce')
@endsection

@section('content')
    <h1>{{ $thread->subject }}</h1>
    <div>
        @each('messenger.partials.messages', $thread->messages, 'message')
        {{-- Filtrar si no es el propietario y el mensaje es de solo lectura --}}
        @if(Auth::id() != $thread->owner_id && $thread->noreply == true)
            <p>{{ __('This is a read-only thread.') }}</p>
            <div class="form-group">
                @include('partials.backbutton')
            </div>
        @else
            @include('messenger.partials.form-message')
        @endif
    </div>
@stop
