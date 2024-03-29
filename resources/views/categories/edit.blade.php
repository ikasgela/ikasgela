@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit category')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($category, ['route' => ['categories.update', $category->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('period_id', __('Period'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="period_id" name="period_id">
                        @foreach($periods as $period)
                            <option
                                value="{{ $period->id }}" {{ $category->period_id == $period->id ? 'selected' : '' }}>
                                {{ $period->full_name }}
                            </option>
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
