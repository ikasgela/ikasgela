@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Qualifications')])

    @if(Auth::user()->hasAnyRole(['admin']))
        @include('partials.recursos.filtro_curso', ['ruta' => 'qualifications.index.filtro'])
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('qualifications.create') }}">{{ __('New qualification') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($qualifications as $qualification)
                <tr>
                    <td>{{ $qualification->id }}</td>
                    <td>{{ $qualification->curso->full_name }}</td>
                    <td>{{ $qualification->name }}</td>
                    <td>{{ $qualification->description }}</td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_editar', ['ruta' => 'qualifications', 'recurso' => $qualification])
                            {{ html()->form('DELETE', route('qualifications.destroy', $qualification->id))->open() }}
                            @include('partials.boton_borrar', ['last' => true])
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
