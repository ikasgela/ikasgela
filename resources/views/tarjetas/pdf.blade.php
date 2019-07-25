@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>Tarjeta: PDF</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">Subida de fichero</div>
                <div class="card-body">
                    <p class="card-text">Sube aquí una imagen con tu solución.</p>

                    <form action="{{ route('uploadfile') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="form-group">
                            <input type="file" name="file" id="">
                            <span class="help-block text-danger">{{$errors->first('file')}}</span>
                        </div>
                        <button class="btn btn-primary">Upload</button>
                    </form>

                    <hr>
                    <p>
                        <i class="fas fa-file-pdf mr-2" style="font-size:2em;color:#D70909"></i>
                        <a href="{{ url('/pdf/test.pdf') }}" target="_blank"> Archivo PDF</a>
                    </p>
                    <p>
                        <i class="fas fa-file-word mr-2" style="font-size:2em;color:#295699"></i>
                        <a href="{{ url('/pdf/test.pdf') }}" target="_blank"> Archivo de Microsoft Word.</a>
                    </p>

                </div>
                <div class="card-footer d-flex flex-row justify-content-between">
                    <div>Máximo de ficheros: 3</div>
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>
@endsection
