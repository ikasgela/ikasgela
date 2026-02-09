@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Configure options')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($safe_exam, 'PUT', route('safe_exam.update', $safe_exam->id))->open() }}

            @include('components.label-check', [
                'label' => __('Full screen'),
                'name' => 'full_screen',
            ])

            @include('components.label-check', [
                'label' => __('Show toolbar'),
                'name' => 'show_toolbar',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
