@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Resources: IntelliJ projects')])

    <div class="row">
        <div class="col-md-12">
            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</div>
                <div class="card-body pb-1">
                    <h2>{{ $actividad->nombre }}</h2>
                    <p>{{ $actividad->descripcion }}</p>
                </div>
            </div>
            {{-- Fin tarjeta--}}
        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Assigned resources')])

    @if(count($intellij_projects) > 0 )
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('Repository') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($intellij_projects as $intellij_project)
                    <tr>
                        <td>{{ $intellij_project->id }}</td>
                        <td>{{ $intellij_project->repositorio }}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('intellij_projects.desasociar', ['actividad' => $actividad->id, '$intellij_project'=>$intellij_project->id]) }}">
                                @csrf
                                @method('DELETE')
                                <div class='btn-group'>
                                    @include('partials.boton_borrar')
                                </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    @include('partials.subtitulo', ['subtitulo' => __('Available resources')])

    @if(count($disponibles) > 0 )
        <form method="POST" action="{{ route('intellij_projects.asociar', ['actividad' => $actividad->id]) }}">
            @csrf

            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>{{ __('Repository') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($disponibles as $intellij_project)
                        <tr>
                            <td><input type="checkbox" name="seleccionadas[]" value="{{ $intellij_project->id }}"></td>
                            <td>{{ $intellij_project->id }}</td>
                            <td>@include('partials.link_gitlab', ['proyecto' => $intellij_project->gitlab() ])</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @include('layouts.errors')

            <div>
                <button type="submit" class="btn btn-primary mb-4">{{ __('Save assigment') }}</button>
            </div>

        </form>
    @else
        <div class="row">
            <div class="col-md">
                <p>No hay elementos.</p>
            </div>
        </div>
    @endif

    <div>
        @include('partials.backbutton')
    </div>
@endsection
