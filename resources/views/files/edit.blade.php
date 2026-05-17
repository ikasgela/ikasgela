@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit file')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($file, 'PUT', route('files.update', $file->id))->open() }}

            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'description',
                'placeholder' => $file->title
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
