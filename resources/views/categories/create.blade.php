@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New category')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('categories.store'))->open() }}

            @include('components.label-select', [
                'label' => __('Period'),
                'name' => 'period_id',
                'coleccion' => $periods,
                'opcion' => function ($period) {
                        return html()->option($period->full_name,
                            $period->id,
                            old('period_id') == $period->id);
                },
            ])

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])
            @include('components.label-text', [
                'label' => __('Slug'),
                'name' => 'slug',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
