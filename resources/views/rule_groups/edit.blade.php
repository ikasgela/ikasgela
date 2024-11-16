@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit rule group')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($rule_group, 'PUT', route('rule_groups.update', $rule_group->id))->open() }}

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
            {{ html()->closeModelForm() }}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Rules')])

    @include('rules.tabla', ['rules' => $rule_group->rules])

    <div class="mb-3">
        <a class="btn btn-primary"
           href="{{ route('rules.anyadir', $rule_group) }}">{{ __('New rule') }}</a>
    </div>

@endsection
