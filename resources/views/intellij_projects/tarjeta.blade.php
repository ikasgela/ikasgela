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
    <div class="card-header d-flex justify-content-between">
        <div>
            @switch($intellij_project->open_with)
                @case('datagrip')
                    <i class="fas fa-table mr-2"></i>{{ __('SQL project') }}
                    @break
                @case('idea')
                    <i class="fab fa-java mr-2"></i>{{ __('Java project') }}
                    @break
                @case('phpstorm')
                    <i class="fa-brands fa-php mr-2"></i>{{ __('PHP project') }}
                    @break
                @default
                    <i class="fab fa-git-alt mr-2"></i>{{ __('Git repository') }}
            @endswitch
        </div>
        <div>
            @if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'profesor.revisar')
                <a title="{{ __('Clear cache') }}"
                   href="{{ route('intellij_projects.borrar_cache_tarea', [$intellij_project->id, $actividad->id]) }}"
                   class='text-dark mr-2'><i class="fas fa-broom"></i></a>
            @endif
            @include('partials.modificar_recursos', ['ruta' => 'intellij_projects'])
            @include('partials.editar_recurso', ['recurso' => $intellij_project, 'ruta' => 'intellij_projects'])
            @if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'profesor.revisar')
                <a title="{{ __('Edit fork') }}"
                   href="{{ route('intellij_projects.edit_fork', [$intellij_project->id, $actividad->id]) }}"
                   class='text-dark'><i class="fas fa-edit"></i></a>
            @endif
        </div>
    </div>
    <div class="card-body">
        @include('partials.cabecera_recurso', ['recurso' => $intellij_project, 'ruta' => 'intellij_projects'])
        @if(isset($actividad) && $actividad->plantilla && Auth::user()->hasRole('alumno'))
            <a href="{{ route('intellij_projects.download', ['intellij_project'=>$intellij_project->id]) }}"
               class="btn btn-primary">{{ __('Download the project') }}</a>
        @elseif(isset($actividad) && !$intellij_project->isForked() && Auth::user()->hasRole('alumno') && !($repositorio['id'] == '?'))
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
            @switch($intellij_project->open_with)
                @case('datagrip')
                    <a href="{{ $intellij_project->datagrip_deep_link() }}"
                       class="btn btn-primary">{{ __('Open in DataGrip') }}</a>
                    @break
                @case('idea')
                    <a href="{{ $intellij_project->intellij_idea_deep_link() }}"
                       class="btn btn-primary">{{ __('Open in IntelliJ IDEA') }}</a>
                    @break
                @case('phpstorm')
                    <a href="{{ $intellij_project->phpstorm_deep_link() }}"
                       class="btn btn-primary">{{ __('Open in PhpStorm') }}</a>
                    @break
                @default
                    <a href="{{ $intellij_project->gitkraken_deep_link() }}"
                       class="btn btn-primary">{{ __('Open in GitKraken') }}</a>
            @endswitch
            <a href="{{ $repositorio['web_url']  }}" target="_blank"
               class="btn btn-secondary">{{ __('Open in Gitea') }}</a>
            @switch($intellij_project->open_with)
                @case('datagrip')
                @case('idea')
                @case('phpstorm')
                    <a href="{{ $intellij_project->gitkraken_deep_link() }}"
                       class="btn btn-secondary">{{ __('Open in GitKraken') }}</a>
            @endswitch
            <div class='btn-group'>
                @if(isset($actividad) && Auth::user()->hasRole('profesor'))
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
            @if(isset($jplags) && Auth::user()->hasRole('profesor') && $intellij_project->open_with == 'idea' && Route::currentRouteName() != 'actividades.preview')
                <h5 class="card-title mt-5">{{ __('JPlag results') }}</h5>
                <div class="table-responsive">
                    <table class="table table-bordered small">
                        <thead class="thead-dark">
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Repository') }}</th>
                            <th>{{ __('Percent') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($jplags as $jplag)
                            @if(!is_null($jplag->match))
                                <tr class="{{ $jplag->percent > 75 ? 'bg-warning text-dark' : '' }}">
                                    <td>
                                        <a href="{{ route('profesor.revisar', ['user' => $jplag->match->user->id, 'tarea' => $jplag->match->id]) }}"
                                           class="{{ $jplag->percent > 75 ? 'text-dark' : '' }}"
                                           target="_blank">
                                            {{ $jplag->match->user->full_name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ $jplag->match->actividad->intellij_projects()->first()->repository()['http_url_to_repo'] }}"
                                           class="{{ $jplag->percent > 75 ? 'text-dark' : '' }}"
                                           target="_blank">
                                            {{ __('Open in Gitea') }}
                                        </a>
                                    </td>
                                    <td>{{ $jplag->percent }}&thinsp;%</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-right">
                        <a href="{{ route('profesor.jplag', ['tarea' => $tarea?->id]) }}"
                           class="btn btn-secondary">{{ __('Update') }}</a>
                        <a href="{{ route('profesor.jplag_download', ['tarea' => $tarea?->id]) }}"
                           class="btn btn-secondary">{{ __('Download') }}</a>
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-danger mb-0 mt-3" role="alert">
                <span>{{ __('Server error, contact with your administrator.') }}</span>
            </div>
        @endif
    </div>
</div>
