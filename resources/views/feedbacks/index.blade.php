@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Feedback messages')])

    @include('partials.subtitulo', ['subtitulo' => __('Course')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('feedbacks.create') }}">{{ __('New feedback message') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($feedbacks as $feedback)
                <tr>
                    <td>{{ $feedback->id }}</td>
                    <td>{{ is_a($feedback->curso, 'App\Actividad') ? $feedback->curso->unidad->codigo.' - ' : '' }}{{ $feedback->curso->nombre }}</td>
                    <td>{{ $feedback->titulo }}</td>
                    <td>
                        {!! Form::open(['route' => ['feedbacks.destroy', $feedback->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('feedbacks.edit', [$feedback->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                            @include('partials.boton_borrar')
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Activities')])

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Activity') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                @foreach($actividad->feedbacks as $feedback)
                    <tr>
                        <td>{{ $feedback->id }}</td>
                        <td>{{ is_a($feedback->curso, 'App\Actividad') ? $feedback->curso->unidad->codigo.' - ' : '' }}{{ $feedback->curso->nombre }}</td>
                        <td>{{ $feedback->titulo }}</td>
                        <td>
                            {!! Form::open(['route' => ['feedbacks.destroy', $feedback->id], 'method' => 'DELETE']) !!}
                            <div class='btn-group'>
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('feedbacks.edit', [$feedback->id]) }}"
                                   class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                @include('partials.boton_borrar')
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
