@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New milestone')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('milestones.store'))->open() }}

            {{ html()->hidden('curso_id', $curso_actual?->id) }}

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])
            @include('components.label-datetime', [
                'label' => __('Date'),
                'name' => 'date',
            ])
            @include('components.label-check', [
                'label' => __('Published'),
                'name' => 'published',
            ])
            @include('components.label-text', [
                'label' => __('Decimals'),
                'name' => 'decimals',
            ])
            @include('components.label-check', [
                'label' => __('Truncate'),
                'name' => 'truncate',
            ])
            @include('components.label-check', [
                'label' => __('Normalize calification'),
                'name' => 'normalizar_nota',
            ])

            <div class="row mb-3">
                <div class="col-sm-2">
                    {{ html()->label(__('Proportional calification adjustment'), 'ajuste_proporcional_nota')->class('col-form-label') }}
                </div>
                <div class="col-sm-10">
                    {{ html()->select('ajuste_proporcional_nota')->class('form-select')->open() }}
                    {{ html()->option(__('--- None --- ')) }}
                    {{ html()->option(__('Average'), 'media', old('ajuste_proporcional_nota') == 'media') }}
                    {{ html()->option(__('Median'), 'mediana', old('ajuste_proporcional_nota') == 'mediana') }}
                    {{ html()->select()->close() }}
                </div>
            </div>

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
