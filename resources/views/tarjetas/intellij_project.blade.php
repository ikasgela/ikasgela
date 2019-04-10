<div class="card">
    <div class="card-header"><i class="fab fa-java"></i> {{ __('IntelliJ project') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $repositorio['name'] }}</h5>
        <p class="card-text">{{ $repositorio['description'] }}</p>
        @if(!$intellij_project->isForked())
            <a href="{{ route('intellij_projects.fork', ['actividad' => $actividad->id, 'intellij_project'=>$intellij_project->id]) }}"
               class="btn btn-primary">{{ __('Clone the project') }}</a>
            @if(session('status'))
                <div class="alert alert-danger mb-0 mt-3" role="alert">
                    <span>{{ session('status') }}</span>
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
