@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit selector')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($selector, 'PUT', route('selectors.update', $selector->id))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Rule groups')])

    <div class="card mb-3">
        <div class="card-body">

            @include('rule_groups.tabla', ['rule_groups' => $rule_groups, 'selector' => $selector])

        </div>
    </div>

    @include('partials.backbutton')

@endsection
