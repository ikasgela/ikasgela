@extends('layouts.app')

@section('tinymce')
    @include('messenger.partials.tinymce')
    @include('mceImageUpload::upload_form', ['upload_url' => route('tinymce.upload.image')])
@endsection

@section('content')

    @include('partials.titular', ['titular' => __('Create new conversation')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['messages.store'], 'id' => 'nuevo_mensaje']) !!}

            {{ Form::campoTexto('subject', __('Subject'), $titulo ?: old('subject')) }}

            <div class="form-group row">
                {!! Form::label('message', __('Message'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <textarea rows="10" class="form-control" id="message"
                              name="message">{!! old('message') !!}</textarea>
                </div>
            </div>
            @if(Auth::user()->hasRole('profesor'))
                @if($users->count() > 0)
                    <div class="form-group row">
                        {!! Form::label('recipients', __('Recipients'), ['class' => 'col-sm-2 col-form-label']) !!}
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
                                               value="{{ $user->id }}" {{ $selected_user == $user->id ? 'checked' : '' }}>
                                        {{$user->name}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                {{ Form::campoCheck('noreply', __('No reply'), false) }}
            @else
                <div class="form-group row">
                    {!! Form::label('recipients', __('Recipient'), ['class' => 'col-sm-2 col-form-label']) !!}
                    <div class="col-sm-10">
                        <select class="form-control" id="recipients" name="recipients[]">
                            @foreach($profesores as $profesor)
                                <option value="{{ $profesor->id }}">{{ $profesor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(Auth::user()->hasRole('admin'))
                {{ Form::campoCheck('alert', __('Alert (overrides notifications settings)'), false) }}
            @endif
            @include('partials.guardar_cancelar',['texto' => __('Send')])
            @include('layouts.errors')
            {!! Form::close() !!}
        </div>
    </div>
@stop
