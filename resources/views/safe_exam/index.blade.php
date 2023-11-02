@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Safe Exam Browser'), 'subtitulo' => ''])

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Course') }}</th>
                <th colspan="2">{{ __('Token') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->full_name }}</td>
                    <td>{{ $curso->token ?: '-' }}</td>
                    <td>
                        <div class='btn-group'>
                            {!! Form::open(['route' => ['safe_exam.reset_token', [$curso->id]], 'method' => 'DELETE']) !!}
                            <button title="{{ __('Reset token') }}"
                                    name="reset_token"
                                    type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                                    class="btn btn-light btn-sm"><i class="fas fa-power-off text-danger"></i>
                            </button>
                            {!! Form::close() !!}
                            {!! Form::open(['route' => ['safe_exam.delete_token', [$curso->id]], 'method' => 'DELETE']) !!}
                            <button title="{{ __('Delete token') }}"
                                    name="delete_token"
                                    type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                                    class="btn btn-light btn-sm"><i class="fas fa-trash text-danger"></i>
                            </button>
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
