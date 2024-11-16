@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New YouTube video')])

    <div class="card">
        <div class="card-body">

            {{ html()->form('POST', route('youtube_videos.store'))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-text', [
                'label' => __('Code'),
                'name' => 'codigo',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
