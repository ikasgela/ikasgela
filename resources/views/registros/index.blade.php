@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Records')])

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Task') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Timestamp') }}</th>
                <th>{{ __('Details') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($registros as $registro)
                <tr>
                    <td>{{ $registro->id }}</td>
                    <td>{{ $registro->user->name }}</td>
                    <td>{{ $registro->tarea->actividad->nombre }}</td>
                    <td>{{ $registro->estado }}</td>
                    <td>{{ $registro->timestamp }}</td>
                    <td>{{ $registro->details }}</td>
                    <td>
                        {!! Form::open(['route' => ['registros.destroy', $registro->id], 'method' => 'DELETE']) !!}
                        <div class='btn-group'>
                            @include('partials.boton_borrar')
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
