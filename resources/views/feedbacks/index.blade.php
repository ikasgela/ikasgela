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
                <th>{{ __('Order') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($feedbacks as $feedback)
                <tr>
                    <td>{{ $feedback->id }}</td>
                    <td>{{ $feedback->comentable->category->period->organization->name }}
                        - {{ $feedback->comentable->category->period->name }}
                        - {{ is_a($feedback->comentable, 'App\Models\Actividad') ? $feedback->comentable->unidad->codigo.' - ' : '' }}{{ $feedback->comentable->nombre }}</td>
                    <td>{{ $feedback->titulo }}</td>
                    <td>
                        @include('partials.botones_reordenar', ['ruta' => 'feedbacks.reordenar'])
                    </td>
                    <td>
                        <div class='btn-group'>
                            @include('partials.boton_editar', ['ruta' => 'feedbacks', 'recurso' => $feedback])
                            {{ html()->form('DELETE', route('feedbacks.destroy', $feedback->id))->open() }}
                            @include('partials.boton_borrar', ['last' => true])
                            {{ html()->form()->close() }}
                        </div>
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
                <th>{{ __('Order') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                @php($ids = $actividad->feedbacks->pluck('id')->toArray())
                @foreach($actividad->feedbacks as $feedback)
                    <tr>
                        <td>{{ $feedback->id }}</td>
                        <td>{{ is_a($feedback->comentable, 'App\Models\Actividad') ? $feedback->comentable->unidad->codigo.' - ' : '' }}{{ $feedback->comentable->nombre }}</td>
                        <td>{{ $feedback->titulo }}</td>
                        <td>
                            @include('partials.botones_reordenar', ['ruta' => 'feedbacks.reordenar'])
                        </td>
                        <td>
                            <div class='btn-group'>
                                @include('partials.boton_editar', ['ruta' => 'feedbacks', 'recurso' => $feedback])
                                {{ html()->form('DELETE', route('feedbacks.destroy', $feedback->id))->open() }}
                                @include('partials.boton_borrar', ['last' => true])
                                {{ html()->form()->close() }}
                            </div>
                        </td>
                    </tr>
                    @if($loop->last)
                        <tr>
                            <td colspan="20">&nbsp;</td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
