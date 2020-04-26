@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Documents') }}</h1>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Title') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($documentos as $documento)
                <tr>
                    <td>{{ $documento->id }}</td>
                    <td>{{ $documento->title }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
