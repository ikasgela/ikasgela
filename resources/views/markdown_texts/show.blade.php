@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Markdown text')])

    <div class="row">
        <div class="col-md-6">

            {{-- Tarjeta --}}
            <div class="card">
                <div class="card-header">{{ $markdown_text->titulo }}</div>
                <div class="card-body">
                    @markdown($texto)
                </div>
            </div>
            {{-- Fin tarjeta--}}

        </div>
    </div>
@endsection