@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Archived')])

    <div class="table-responsive">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Unit') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                {{-- <th>{{ __('Score') }}</th> --}}
            </tr>
            </thead>
            <tbody>
            @foreach($actividades as $actividad)
                <tr>
                    <td class="py-3">{{ $actividad->id }}</td>
                    <td class="py-3">{{ $actividad->unidad->nombre }}</td>
                    <td class="py-3">{{ $actividad->nombre }}</td>
                    <td class="py-3">{{ $actividad->slug }}</td>
                    {{-- <td class="py-3">{{ $actividad->puntuacion }}</td> --}}
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
