@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Settings')])

    <div class="card">
        <div class="card-body">
            {!! Form::open(['route' => ['settings.guardar']]) !!}

            @auth
                @if(Auth::user()->hasRole('admin'))
                    <div class="form-group row">
                        {!! Form::label('curso_id', __('Organization'), ['class' => 'col-sm-2 col-form-label']) !!}
                        <div class="col-sm-10">
                            <select class="form-control" id="organization_id" name="organization_id">
                                @foreach($organizations as $organization)
                                    <option value="{{ $organization->id }}" <?php if (@setting('organization_actual') == $organization->id) echo 'selected'; ?>>{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('period_id', __('Period'), ['class' => 'col-sm-2 col-form-label']) !!}
                        <div class="col-sm-10">
                            <select class="form-control" id="period_id" name="period_id">
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}" <?php if (@setting('period_actual') == $period->id) echo 'selected'; ?>>{{ $period->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            @endauth

            <div class="form-group row">
                {!! Form::label('curso_id', __('Current course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" <?php if (@setting('curso_actual') == $curso->id) echo 'selected'; ?>>{{ $curso->nombre }}
                                - {{ $curso->category->period->name }}</option>
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
