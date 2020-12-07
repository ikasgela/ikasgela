@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Units') }}</h1>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>{{ __('Code') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($unidades as $unidad)
                <tr>
                    <td>{{ $unidad->codigo }}</td>

                    <td>
                        @include('unidades.partials.nombre_con_etiquetas')
                    </td>
                    <td>
                        <div class='btn-group'>
                            {!! Form::open(['route' => ['intellij_projects.descargar.repos'], 'method' => 'POST']) !!}
                            {!! Form::button('<i class="fas fa-download"></i>', ['type' => 'submit',
                                'class' => 'btn btn-light btn-sm', 'title' => __('Download projects')
                            ]) !!}
                            {!! Form::hidden('unidad_id',$unidad->id) !!}
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
