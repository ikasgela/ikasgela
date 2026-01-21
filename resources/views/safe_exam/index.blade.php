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
                <th colspan="2">{{ __('Quit password') }}</th>
                <th>{{ __('Settings') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->full_name }}</td>
                    <td>
                        <pre class="m-0">{{ $curso->safe_exam?->token ?: '-' }}</pre>
                    </td>
                    <td>
                        <div class='btn-group'>
                            {{ html()->form('POST', route('safe_exam.reset_token', $curso->id))->open() }}
                            {{ html()
                                ->submit('<i class="bi bi-arrow-clockwise text-danger"></i>')
                                ->name('reset_token')
                                ->class(['btn btn-light btn-sm me-2'])
                                ->attribute('title', __('Reset token'))
                                ->attribute('onclick', "single_click_confirmar(event, this, '" . __('Confirmation needed') ."', '". __('Are you sure?') ."', '". __('Confirm'). "', '". __('Cancel') ."');")
                            }}
                            {{ html()->form()->close() }}
                            {{ html()->form('DELETE', route('safe_exam.delete_token', $curso->id))->open() }}
                            {{ html()
                                ->submit('<i class="bi bi-trash3 text-danger"></i>')
                                ->name('delete_token')
                                ->class(['btn btn-light btn-sm'])
                                ->attribute('title', __('Delete token'))
                                ->attribute('onclick', "single_click_confirmar(event, this, '" . __('Confirmation needed') ."', '". __('Are you sure?') ."', '". __('Confirm'). "', '". __('Cancel') ."');")
                            }}
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                    <td>
                        <pre class="m-0">{{ $curso->safe_exam?->quit_password ?: '-' }}</pre>
                    </td>
                    <td>
                        <div class='btn-group'>
                            {{ html()->form('POST', route('safe_exam.reset_quit_password', $curso->id))->open() }}
                            {{ html()
                                ->submit('<i class="bi bi-arrow-clockwise text-danger"></i>')
                                ->name('reset_quit_password')
                                ->class(['btn btn-light btn-sm me-2'])
                                ->attribute('title', __('Reset quit password'))
                                ->attribute('onclick', "single_click_confirmar(event, this, '" . __('Confirmation needed') ."', '". __('Are you sure?') ."', '". __('Confirm'). "', '". __('Cancel') ."');")
                            }}
                            {{ html()->form()->close() }}
                            {{ html()->form('DELETE', route('safe_exam.delete_quit_password', $curso->id))->open() }}
                            {{ html()
                                ->submit('<i class="bi bi-trash3 text-danger"></i>')
                                ->name('delete_quit_password')
                                ->class(['btn btn-light btn-sm'])
                                ->attribute('title', __('Delete quit password'))
                                ->attribute('onclick', "single_click_confirmar(event, this, '" . __('Confirmation needed') ."', '". __('Are you sure?') ."', '". __('Confirm'). "', '". __('Cancel') ."');")
                            }}
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('safe_exam.config_seb', [$curso->safe_exam?->id ?: App\Models\SafeExam::create(['curso_id' => $curso->id])->id]) }}"
                           title="{{ __('Download SEB configuration file') }}"
                           class="btn btn-sm btn-light me-2" role="button">
                            <i class="bi bi-download"></i>
                        </a>
                        <a href="{{ route('safe_exam.allowed', [$curso->safe_exam?->id ?: App\Models\SafeExam::create(['curso_id' => $curso->id])->id]) }}"
                           title="{{ __('Allowed apps and URLs') }}"
                           class="btn btn-sm btn-light" role="button">
                            <i class="bi bi-shield-check"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
