@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit organization')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($organization, ['route' => ['organizations.update', $organization->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            {{ Form::campoCheck('registration_open', __('Registration open')) }}
            {{ Form::campoTexto('seats', __('Available seats')) }}

            <div class="form-group row">
                {!! Form::label('current_period_id', __('Current period'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="current_period_id" name="current_period_id">
                        <option value="">{{ __('--- None --- ') }}</option>
                        @foreach($organization->periods as $period)
                            <option value="{{ $period->id }}" <?php if ($organization->current_period_id == $period->id) echo 'selected'; ?>>{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
