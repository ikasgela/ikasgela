@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Download projects')])

    @include('partials.subtitulo', ['subtitulo' => __('Course')])

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ Auth::user()->curso_actual()?->full_name }}</td>
                <td>
                    <div class='btn-group'>
                        {!! Form::open(['route' => ['intellij_projects.descargar.plantillas.curso'], 'method' => 'POST']) !!}
                        {!! Form::button('<i class="fas fa-download text-danger"></i>', ['type' => 'submit',
                            'class' => 'btn btn-light btn-sm ms-3', 'title' => __('Download template projects')
                        ]) !!}
                        {!! Form::close() !!}
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Units')])

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
                                'class' => 'btn btn-light btn-sm', 'title' => __('Download user projects')
                            ]) !!}
                            {!! Form::hidden('unidad_id',$unidad->id) !!}
                            {!! Form::close() !!}

                            {!! Form::open(['route' => ['intellij_projects.descargar.plantillas'], 'method' => 'POST']) !!}
                            {!! Form::button('<i class="fas fa-download text-danger"></i>', ['type' => 'submit',
                                'class' => 'btn btn-light btn-sm ms-3', 'title' => __('Download template projects')
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
