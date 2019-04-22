@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New category')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['categories.store']]) !!}

            <div class="form-group row">
                {!! Form::label('period_id', __('Period'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="period_id" name="period_id">
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
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
