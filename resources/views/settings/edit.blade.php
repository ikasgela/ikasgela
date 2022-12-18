@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Settings'), 'subtitulo' => ''])

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => trans('tutorial.cambiar_curso')
    ])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['settings.guardar']]) !!}

            <div class="form-group row">
                {!! Form::label('curso_id', __('Current course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        <option value="">{{ __('--- None --- ') }}</option>
                        @foreach($cursos as $curso)
                            <option
                                value="{{ $curso->id }}" <?php if (@setting('curso_actual') == $curso->id) echo 'selected'; ?>>
                                {{ $curso->category->period->organization->name }}
                                - {{ $curso->nombre }}
                                - {{ $curso->category->period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>

    @auth
        @if(Auth::user()->hasRole('profesor'))

            @include('partials.subtitulo', ['subtitulo' => __('Teacher')])

            {!! Form::open(['route' => ['settings.guardar']]) !!}
            <div class="form-group d-flex flex-row justify-content-between">
                {!! Form::label('organization_id', __('Organization'), ['class' => 'col-form-label']) !!}
                <div class="flex-fill mx-3">
                    <select class="form-control" id="organization_id" name="organization_id">
                        <option value="">{{ __('--- None ---') }}</option>
                        @foreach($organizations as $organization)
                            <option
                                value="{{ $organization->id }}" <?php if (@setting('_organization_id') == $organization->id) echo 'selected'; ?>>
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">{{ __('Change') }}</button>
                </div>
            </div>
            {!! Form::close() !!}

            <div class="card">
                <div class="card-body">

                    {!! Form::open(['route' => ['settings.guardar']]) !!}

                    <div class="form-group row">
                        {!! Form::label('period_id', __('Current period'), ['class' => 'col-sm-2 col-form-label']) !!}
                        <div class="col-sm-10">
                            <select class="form-control" id="period_id" name="period_id">
                                <option value="">{{ __('--- None ---') }}</option>
                                @foreach($periods as $period)
                                    <option
                                        value="{{ $period->id }}" <?php if (@setting('_period_id') == $period->id) echo 'selected'; ?>>
                                        {{ $period->organization->name }} - {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>

                    @include('layouts.errors')
                    {!! Form::close() !!}

                </div>
            </div>

            {!! Form::close() !!}
        @endif
    @endauth

    @include('partials.subtitulo', ['subtitulo' => __('Course data')])
    <div class="card">
        <div class="card-body">
            <div class="form-group row">
                {!! Form::label('intellij_projects', __('Intellij Projects'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    {!! Form::open(['route' => ['intellij_projects.descargar'], 'method' => 'POST']) !!}
                    {!! Form::button(__('Download script'), ['type' => 'submit',
                        'class' => 'btn btn-primary'
                    ]) !!}
                    {!! Form::close() !!}
                    <p class="small m-0 mt-2">{{ __('Click on the button to download the script and run it on your
                        computer. You will need Git installed. On Windows the script can be run from Git Bash.') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
