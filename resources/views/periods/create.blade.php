@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New period')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['periods.store']]) !!}

            <div class="form-group row">
                {!! Form::label('organization_id', __('Organization'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="organization_id" name="organization_id">
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
