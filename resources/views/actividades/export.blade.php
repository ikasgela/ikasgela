<div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
    <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>{{ __('Unit') }}</th>
            <th>{{ __('Name') }}</th>
            <th colspan="2">{{ __('Resources') }}</th>
            <th>{{ __('Tags') }}</th>
        </tr>
        </thead>
        <tbody>
        @php($alterna = true)
        @php($actividad_anterior = null)
        @php($recurso_anterior = null)
        @foreach($actividades as $actividad)
            @foreach($actividad->recursos as $recurso)
                <tr>
                    <td style="{{ $actividad->destacada ? 'background-color: #ffc107' : ($alterna ? 'background-color: #cccccc' : '') }}">
                        {{ $actividad->unidad->id != $actividad_anterior?->unidad->id ? $actividad->unidad->nombre : '' }}
                    </td>
                    <td style="{{ $actividad->destacada ? 'background-color: #ffc107' : ($alterna ? 'background-color: #cccccc' : '') }}">
                        {{ $recurso->pivot->actividad_id != $recurso_anterior?->pivot->actividad_id ? $actividad->nombre : '' }}
                    </td>
                    <td style="{{ $actividad->destacada ? 'background-color: #ffc107' : ($alterna ? 'background-color: #cccccc' : '') }}">
                        @switch($recurso::class)
                            @case('App\Models\IntellijProject')
                            IJ
                            @break
                            @case('App\Models\MarkdownText')
                            MD
                            @break
                            @case('App\Models\YoutubeVideo')
                            YT
                            @break
                            @case('App\Models\FileUpload')
                            UP
                            @break
                            @case('App\Models\FileResource')
                            F
                            @break
                            @case('App\Models\Cuestionario')
                            C
                            @break
                        @endswitch
                        &nbsp;&nbsp;
                    </td>
                    <td style="{{ $actividad->destacada ? 'background-color: #ffc107' : ($alterna ? 'background-color: #cccccc' : '') }}">
                        {{ $recurso->titulo }}
                    </td>
                    <td style="{{ $actividad->destacada ? 'background-color: #ffc107' : ($alterna ? 'background-color: #cccccc' : '') }}">
                        {{ $recurso->pivot->actividad_id != $recurso_anterior?->pivot->actividad_id ? $actividad->tags : '' }}
                    </td>
                </tr>
                @php($recurso_anterior = $recurso)
                @php($actividad_anterior = $actividad)
            @endforeach
            @php($alterna = !$alterna)
        @endforeach
        </tbody>
    </table>
</div>

<div>
    <p class="text-center text-muted font-xs">{{ now()->isoFormat('LLLL') }}</p>
</div>
