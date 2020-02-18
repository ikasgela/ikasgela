@if($intellij_project->isForking())
    @push('intellij-isforking')
        <script>
            (function () {
                setInterval(function () {
                    axios.get('{{ route('intellij_projects.is_forking', ['actividad' => $actividad->id, 'intellij_project'=>$intellij_project->id]) }}')
                        .then(function (response) {
                            if (response.data === 2 || response.data === 3) {
                                location.reload();
                            }
                        });
                }, 2000);
            })();
        </script>
    @endpush
@endif

<div class="card">
    <div class="card-header"><i class="fab fa-java"></i> {{ __('IntelliJ project') }}</div>
    <div class="card-body">
        <h5 class="card-title">{{ $intellij_project->titulo }}</h5>
        <p class="card-text">{{ $intellij_project->descripcion }}</p>
        @if(!$intellij_project->isForked() && Auth::user()->hasRole('alumno') && !$repositorio['id'] == '?')
            @if($intellij_project->getForkStatus() == 0)
                <a href="{{ route('intellij_projects.fork', ['actividad' => $actividad->id, 'intellij_project'=>$intellij_project->id]) }}"
                   class="btn btn-primary single_click">
                    <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Clone the project') }}</a>
            @elseif($intellij_project->getForkStatus() == 1)
                <a href="#" class="btn btn-primary disabled mr-3">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('Clone the project') }}
                </a>
                {{ __('Cloning, please wait...') }}
            @elseif($intellij_project->getForkStatus() == 3)
                <div class="alert alert-danger mb-0 mt-3" role="alert">
                    <span>{{ __('Server error, contact with your administrator.') }}</span>
                </div>
            @endif
        @elseif(isset($repositorio['web_url']))
            <a href="jetbrains://idea/checkout/git?checkout.repo={{ str_replace('https://',"https://".Auth::user()->username."@",$repositorio['http_url_to_repo']) }}&idea.required.plugins.id=Git4Idea"
               class="btn btn-primary">{{ __('Open in IntelliJ IDEA') }}</a>
            <a href="{{ $repositorio['web_url']  }}" target="_blank"
               class="btn btn-secondary">{{ $intellij_project->host == 'gitlab' ? __('Open in GitLab') : __('Open in Gitea') }}</a>
            <div class='btn-group'>
                @if(Auth::user()->hasRole('profesor'))
                    @if($intellij_project->isArchivado())
                        {!! Form::open(['route' => ['intellij_projects.unlock', $intellij_project->id, $actividad->id], 'method' => 'POST']) !!}
                        <button title="{{ __('Unlock') }}"
                                type="submit"
                                class="btn btn-light">
                            <i class="fas fa-lock"></i>
                        </button>
                        {!! Form::close() !!}
                    @else
                        {!! Form::open(['route' => ['intellij_projects.lock', $intellij_project->id, $actividad->id], 'method' => 'POST']) !!}
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
                <span>{{ __('Server error, contact with your administrator.') }}</span>
            </div>
        @endif
    </div>
</div>
