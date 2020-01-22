<div class="card">
    <div class="card-header"><i class="fab fa-java"></i> {{ __('IntelliJ project') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $intellij_project->titulo }}</h5>
        <p class="card-text">{{ $intellij_project->descripcion }}</p>
        @if(!$intellij_project->isForked() && Auth::user()->hasRole('alumno'))
            @if(!$intellij_project->isForking())
                <a href="{{ route('intellij_projects.fork', ['actividad' => $actividad->id, 'intellij_project'=>$intellij_project->id]) }}"
                   class="btn btn-primary single_click">
                    <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Clone the project') }}</a>
            @else
                <div class="alert alert-success mb-0 mt-3" role="alert">
                    <span>{{ __('Cloning, you will receive an email on completion.') }}</span>
                </div>
            @endif
            @if(session('clone_error_id') == $actividad->id)
                <div class="alert alert-danger mb-0 mt-3" role="alert">
                    <span>{{ session('clone_error_status') }}</span>
                </div>
            @endif
        @elseif(isset($repositorio['web_url']))
            <a href="jetbrains://idea/checkout/git?checkout.repo={{ str_replace('https://',"https://".Auth::user()->username."@",$repositorio['http_url_to_repo']) }}&idea.required.plugins.id=Git4Idea"
               class="btn btn-primary">{{ __('Open in IntelliJ IDEA') }}</a>
            <a href="{{ $repositorio['web_url']  }}" target="_blank"
               class="btn btn-secondary">{{ __('Open in GitLab') }}</a>
            <div class='btn-group'>
                @if(Auth::user()->hasRole('profesor'))
                    @if($repositorio['archived'])
                        {!! Form::open(['route' => ['intellij_projects.unlock', $repositorio['id']], 'method' => 'POST']) !!}
                        <button title="{{ __('Unlock') }}"
                                type="submit"
                                class="btn btn-light">
                            <i class="fas fa-lock"></i>
                        </button>
                        {!! Form::close() !!}
                    @else
                        {!! Form::open(['route' => ['intellij_projects.lock', $repositorio['id']], 'method' => 'POST']) !!}
                        <button title="{{ __('Lock') }}"
                                type="submit"
                                class="btn btn-light">
                            <i class="fas fa-unlock"></i>
                        </button>
                        {!! Form::close() !!}
                    @endif
                @endif
            </div>
        @else
            <div class="alert alert-danger mb-0 mt-3" role="alert">
                <span>{{ __('GitLab error, contact with your administrator.') }}</span>
            </div>
        @endif
    </div>
</div>
