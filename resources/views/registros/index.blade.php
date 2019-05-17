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
                    <td>
                        @switch($registro->estado)
                            @case(10)   {{-- Nueva --}}
                            Nueva
                            @break
                            @case(20)   {{-- Aceptada --}}
                            Aceptada
                            @break
                            @case(30)   {{-- Enviada --}}
                            Enviada
                            @break
                            @case(31)   {{-- Reiniciada --}}
                            Reiniciada
                            @break
                            @case(40)   {{-- Revisada: OK --}}
                            @case(41)   {{-- Revisada: ERROR --}}
                            {{ $registro->detalles }}
                            @break;
                            @case(50)   {{-- Terminada --}}
                            Terminada
                            @break
                            @case(60)   {{-- Archivada --}}
                            Archivada
                            @break
                            @default
                        @endswitch
                    </td>
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
