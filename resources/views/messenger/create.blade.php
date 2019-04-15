@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Create new conversation')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['messages.store']]) !!}

            {{ Form::campoTexto('subject', __('Subject'), old('subject')) }}
            {{ Form::campoTextArea('message', __('Message'), old('message')) }}

            @if(Auth::user()->hasRole('profesor'))
                @if($users->count() > 0)
                    <div class="form-group row">
                        {!! Form::label('recipients', __('Recipients'), ['class' => 'col-sm-2 col-form-label']) !!}
                        <div class="col-sm-10">
                            @foreach($users as $user)
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label col-form-label">
                                        <input class="form-check-input" type="checkbox" name="recipients[]"
                                               value="{{ $user->id }}">
                                        {{$user->name}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="form-group row">
                    {!! Form::label('recipients', __('Recipient'), ['class' => 'col-sm-2 col-form-label']) !!}
                    <div class="col-sm-10">
                        <select class="form-control" id="recipients" name="recipients[]">
                            <option value="{{ $profesor->id }}">{{ $profesor->name }}</option>
                        </select>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                <a href="{{ route('messages') }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            </div>

            @include('layouts.errors')
            {!! Form::close() !!}
        </div>
    </div>
@stop
