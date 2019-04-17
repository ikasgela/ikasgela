<div class="card">
    <div class="card-header"><i class="fab fa-java"></i> {{ __('IntelliJ project') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $intellij_project->titulo }}</h5>
        <p class="card-text">{{ $intellij_project->descripcion }}</p>
        @if(!$intellij_project->isForked() && Auth::user()->hasRole('alumno'))
            <a href="{{ route('intellij_projects.fork', ['actividad' => $actividad->id, 'intellij_project'=>$intellij_project->id]) }}"
               class="btn btn-primary">{{ __('Clone the project') }}</a>
            @if(session('clone_error_id') == $actividad->id)
                <div class="alert alert-danger mb-0 mt-3" role="alert">
                    <span>{{ session('clone_error_status') }}</span>
                </div>
            @endif
        @else
            <a href="jetbrains://idea/checkout/git?checkout.repo={{ str_replace('https://',"https://".Auth::user()->username."@",$repositorio['http_url_to_repo']) }}&idea.required.plugins.id=Git4Idea"
               class="btn btn-primary">{{ __('Open in IntelliJ IDEA') }}</a>
            <a href="{{ $repositorio['web_url']  }}" target="_blank"
               class="btn btn-secondary">{{ __('Open in GitLab') }}</a>
        @endif
    </div>
</div>
