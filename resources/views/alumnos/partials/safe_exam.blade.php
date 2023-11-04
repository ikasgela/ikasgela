@if(isset($user->curso_actual()->safe_exam?->token) && $user->num_actividades_en_curso_seb() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @if(!$user->curso_actual()?->token_valido())
                    <div class="card-header text-dark bg-warning">
                        <span><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="ml-2">{{ __("Safe Exam Browser required") }}</span></span>
                    </div>
                    <div class="card-body">
                        <p>{{ __("Some of the tasks require Safe Exam Browser to access them.") }}</p>
                        <a href="{{ $sebs_url }}"
                           class="btn btn-primary">{{ __('Open Safe Exam Browser') }}</a>
                    </div>
                @else
                    <div class="card-header text-dark bg-warning">
                        <span><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="ml-2">{{ __("Safe Exam Browser") }}</span></span>
                    </div>
                    <div class="card-body">
                        <p>{{ __("You are currently in a Safe Exam Browser session.") }}</p>
                        <a href="{{ $sebs_exit_url }}"
                           class="btn btn-primary">{{ __('Quit Safe Exam Browser') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
