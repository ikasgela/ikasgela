@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit questionnaire')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($cuestionario, 'PUT', route('cuestionarios.update', $cuestionario->id))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-check', [
                'label' => __('Template'),
                'name' => 'plantilla',
            ])
            @include('components.label-check', [
                'label' => __('Answered'),
                'name' => 'respondido',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Questions')])

    @include('preguntas.tabla')

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('preguntas.anyadir', $cuestionario) }}">{{ __('Add question') }}</a>
    </div>
@endsection
