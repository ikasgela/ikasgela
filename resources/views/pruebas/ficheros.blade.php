@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Tarjeta: Ficheros de usuario</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">All Files</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>File</th>
                            <th>Caption</th>
                            <th>Size</th>
                            <th>Uploaded</th>
                        </tr>
                        @foreach ($files as $file)
                            <tr>
                                <th><img loading="lazy" style="width:100px" src="{{$file->url}}"></th>
                                <td>{{$file->title}}</td>
                                <td>{{$file->size_in_kb}} KB</td>
                                <td>{{$file->uploaded_time}}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>

@endsection
