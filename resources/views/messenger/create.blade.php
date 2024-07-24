@extends('layouts.app')

@section('tinymce')
    @include('messenger.partials.tinymce')
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('Create new conversation')])

    <div class="card mb-3">
        <div class="card-body pe-0">

            {{ html()->form('POST', route('messages.store'))->open() }}

            <div class="input-group row mb-3">
                {{ html()->label(__('Subject'), 'subject')->class('col-sm-2 col-form-label') }}
                <div class="col-sm-10">
                    {{ html()->text('subject', $titulo ?: old('subject'))->class('form-control') }}
                </div>
            </div>

            <div class="input-group row mb-3">
                {{ html()->label(__('Message'), 'message')->class('col-sm-2 col-form-label') }}
                <div class="col-sm-10">
                    <textarea rows="10" class="form-control" id="message"
                              name="message">{!! old('message') !!}</textarea>
                </div>
            </div>
            @if(Auth::user()->hasRole('profesor'))
                @if($users->count() > 0)
                    <div class="input-group row mb-3">
                        {{ html()->label(__('Recipients'), 'recipients')->class('col-sm-2 col-form-label') }}
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label col-form-label">
                                    <input class="form-check-input" type="checkbox"
                                           id="seleccionar_todos"> {{ __('All') }}
                                </label>
                            </div>
                            @foreach($users as $user)
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label col-form-label">
                                        <input class="form-check-input" type="checkbox" name="recipients[]"
                                               value="{{ $user->id }}" {{ in_array($user->id, $selected_users) ? 'checked' : '' }}>
                                        {{ $user->full_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="input-group row mb-3">
                    {{ html()->label(__('No reply'), 'noreply')->class('col-sm-2 col-form-label') }}
                    <div class="col-sm-10">
                        {{ html()->checkbox('noreply', false)->class('form-check-input ms-0 mt-2') }}
                    </div>
                </div>
            @else
                <div class="input-group row mb-3">
                    {{ html()->label(__('Recipient'), 'recipients')->class('col-sm-2 col-form-label') }}
                    <div class="col-sm-10">
                        <select class="form-select" id="recipients" name="recipients[]">
                            @foreach($profesores as $profesor)
                                <option value="{{ $profesor->id }}">
                                    {{ $profesor->name }} {{ $profesor->surname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(Auth::user()->hasRole('admin'))
                <div class="input-group row mb-3">
                    {{ html()->label(__('Alert (overrides notifications settings)'), 'alert')->class('col-sm-2 col-form-label') }}
                    <div class="col-sm-10">
                        {{ html()->checkbox('alert', false)->class('form-check-input ms-0 mt-2') }}
                    </div>
                </div>
            @endif
            @include('partials.guardar_cancelar',['texto' => __('Send')])
            @include('layouts.errors')
            {{ html()->form()->close() }}
        </div>
    </div>
@endsection
