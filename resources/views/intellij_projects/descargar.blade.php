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
                        {{ html()->form('POST', route('intellij_projects.descargar.plantillas.curso'))->open() }}
                        {{ html()->submit('<i class="bi bi-download text-danger"></i>')->class(['btn btn-light btn-sm'])->attribute('title', __('Download template projects')) }}
                        {{ html()->form()->close() }}
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
                        <div class="d-flex align-items-center">
                            @include('unidades.partials.nombre_con_etiquetas')
                        </div>
                    </td>
                    <td>
                        <div class='btn-group'>
                            {{ html()->form('POST', route('intellij_projects.descargar.repos'))->open() }}
                            {{ html()->submit('<i class="bi bi-download"></i>')->class(['btn btn-light btn-sm', 'me-2'])->attribute('title', __('Download user projects')) }}
                            {{ html()->hidden('unidad_id', $unidad->id) }}
                            {{ html()->form()->close() }}

                            {{ html()->form('POST', route('intellij_projects.descargar.plantillas'))->open() }}
                            {{ html()->submit('<i class="bi bi-download text-danger"></i>')->class(['btn btn-light btn-sm'])->attribute('title', __('Download template projects')) }}
                            {{ html()->hidden('unidad_id', $unidad->id) }}
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
