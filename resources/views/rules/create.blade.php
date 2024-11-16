@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New rule')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('rules.store'))->open() }}

            @include('components.label-value', [
                'label' => __('Rule group'),
                'name' => 'rule_group_id',
                'value' => $rule_group->id,
                'hidden' => $rule_group->id,
            ])
            @include('components.label-text', [
                'label' => __('Property'),
                'name' => 'propiedad',
                'placeholder' => 'puntuacion | intentos',
            ])
            @include('components.label-text', [
                'label' => __('Operator'),
                'name' => 'operador',
                'placeholder' => '> | < | >= | <= | == | !=',
            ])
            @include('components.label-text', [
                'label' => __('Value'),
                'name' => 'valor',
                'placeholder' => '100',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
