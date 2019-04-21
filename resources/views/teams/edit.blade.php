@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit team')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($team, ['route' => ['teams.update', $team->id], 'method' => 'PUT']) !!}

            <div class="form-team row">
                {!! Form::label('group_id', __('Group'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="group_id" name="group_id">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" <?php if ($team->group_id == $group->id) echo 'selected'; ?>>{{ $group->name }}</option>
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
