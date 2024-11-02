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
                            <button title="{{ __('Reset token') }}"
                                    name="reset_token"
                                    type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                                    class="btn btn-light btn-sm"><i class="fas fa-redo text-danger"></i>
                            </button>
                            {{ html()->form()->close() }}
                            {{ html()->form('DELETE', route('safe_exam.delete_token', $curso->id))->open() }}
                            <button title="{{ __('Delete token') }}"
                                    name="delete_token"
                                    type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                                    class="btn btn-light btn-sm"><i class="fas fa-trash text-danger"></i>
                            </button>
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                    <td>
                        <pre class="m-0">{{ $curso->safe_exam?->quit_password ?: '-' }}</pre>
                    </td>
                    <td>
                        <div class='btn-group'>
                            {{ html()->form('POST', route('safe_exam.reset_quit_password', $curso->id))->open() }}
                            <button title="{{ __('Reset quit password') }}"
                                    name="reset_quit_password"
                                    type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                                    class="btn btn-light btn-sm"><i class="fas fa-redo text-danger"></i>
                            </button>
                            {{ html()->form()->close() }}
                            {{ html()->form('DELETE', route('safe_exam.delete_quit_password', $curso->id))->open() }}
                            <button title="{{ __('Delete quit password') }}"
                                    name="delete_quit_password"
                                    type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                                    class="btn btn-light btn-sm"><i class="fas fa-trash text-danger"></i>
                            </button>
                            {{ html()->form()->close() }}
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('safe_exam.allowed', [$curso->safe_exam?->id ?: App\Models\SafeExam::create(['curso_id' => $curso->id])->id]) }}"
                           title="{{ __('Allowed apps and URLs') }}"
                           class="btn btn-sm btn-light" role="button">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
