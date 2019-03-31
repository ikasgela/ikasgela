@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Dashboard') }}</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md">

            <div class="card">
                <div class="card-header">{{ __('Your progress') }}</div>
                <div class="card-body">
                    <div class="progress-group">
                        <div class="progress-group-header align-items-end">
{{--
                            <i class="fas fa-globe-europe progress-group-icon"></i>
--}}
                            <div>Tema 1</div>
                            <div class="ml-auto font-weight-bold mr-2">167 puntos</div>
                            <div class="text-muted small">(56%)</div>
                        </div>
                        <div class="progress-group-bars">
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: 56%" aria-valuenow="56"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
