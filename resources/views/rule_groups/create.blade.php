@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New rule group')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('rule_groups.store'))->open() }}

            @include('components.label-value', [
                'label' => __('Selector'),
                'name' => 'selector_id',
                'value' => $selector->titulo,
                'hidden' => $selector->id,
            ])
            @include('components.label-text', [
                'label' => __('Operator'),
                'name' => 'operador',
                'placeholder' => 'and | or',
            ])
            @include('components.label-text', [
                'label' => __('Action'),
                'name' => 'accion',
                'placeholder' => 'siguiente',
            ])
            @include('components.label-text', [
                'label' => __('Result'),
                'name' => 'resultado',
                'placeholder' => '10 (id_actividad)',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
