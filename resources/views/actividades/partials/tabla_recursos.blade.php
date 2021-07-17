<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>{{ __('Resource') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Order') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividad->recursos as $recurso)
            <tr class="table-cell-click" data-href="{{ route('actividades.preview', [$actividad->id]) }}">
                <td>{{ $recurso->titulo }} {{ $recurso::class }}</td>
                <td>@switch($recurso::class)
                        @case('App\IntellijProject')
                        {{ __('IntelliJ project') }}
                        @break
                        @case('App\MarkdownText')
                        {{ __('Markdown text') }}
                        @break
                        @case('App\YoutubeVideo')
                        {{ __('YouTube video') }}
                        @break
                        @case('App\FileUpload')
                        {{ __('Image upload') }}
                        @break
                        @case('App\FileResource')
                        {{ __('Files') }}
                        @break
                        @case('App\Cuestionario')
                        {{ __('Questionnaire') }}
                        @break
                        @default
                        {{ __('Unknown') }}
                    @endswitch
                </td>
                <td>
                    <div class='btn-group'>
                        {!! Form::open(['route' => ['actividades.reordenar_recursos', $actividad->id], 'method' => 'POST']) !!}
                        <button title="{{ __('Up') }}"
                                type="submit"
                                {{ !isset($ids[$loop->index-1]) ? 'disabled' : '' }}
                                class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <input type="hidden" name="a1" value="{{ $ids[$loop->index] }}">
                        <input type="hidden" name="a2" value="{{ $ids[$loop->index-1] ?? -1 }}">
                        {!! Form::close() !!}

                        {!! Form::open(['route' => ['actividades.reordenar_recursos', $actividad->id], 'method' => 'POST']) !!}
                        <button title="{{ __('Down') }}"
                                type="submit"
                                {{ !isset($ids[$loop->index+1]) ? 'disabled' : '' }}
                                class="btn btn-light btn-sm ml-1">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                        <input type="hidden" name="a1" value="{{ $ids[$loop->index] }}">
                        <input type="hidden" name="a2" value="{{ $ids[$loop->index+1] ?? -1 }}">
                        {!! Form::close() !!}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
