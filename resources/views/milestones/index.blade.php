@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Milestones')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('milestones.create') }}">{{ __('New milestone') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Course') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Date') }}</th>
                <th class="text-center">{{ __('Published') }}</th>
                <th class="text-center">{{ __('Decimals') }}</th>
                <th class="text-center">{{ __('Truncate') }}</th>
                <th class="text-center">{{ __('Normalize calification') }}</th>
                <th class="text-center">{{ __('Proportional calification adjustment') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($milestones as $milestone)
                <tr>
                    <td>{{ $milestone->id }}</td>
                    <td>{{ $milestone->curso->full_name }}</td>
                    <td>{{ $milestone->name }}</td>
                    <td>{{ $milestone->date }}</td>
                    <td class="text-center">@include('partials.check_yes_no', ['checked' => $milestone->published])</td>
                    <td class="text-center">{{ $milestone->decimals }}</td>
                    <td class="text-center">@include('partials.check_yes_no', ['checked' => $milestone->truncate])</td>
                    <td class="text-center">@include('partials.check_yes_no', ['checked' => $milestone->normalizar_nota])</td>
                    <td class="text-center">
                        @switch($milestone->ajuste_proporcional_nota)
                            @case('media')
                                {{ __('Average') }}
                                @break
                            @case('mediana')
                                {{ __('Median') }}
                                @break
                            @default
                                {{ __('Undefined') }}
                        @endswitch
                    </td>
                    <td class="text-nowrap">
                        <div class='btn-group'>
                            @include('partials.boton_editar', ['ruta' => 'milestones', 'recurso' => $milestone])
                            {{ html()->form('DELETE', route('milestones.destroy', $milestone->id))->open() }}
                            @include('partials.boton_borrar', ['last' => true])
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
