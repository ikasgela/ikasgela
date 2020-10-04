<div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
    <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>{{ __('Unit') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Tags') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actividades as $actividad)
            <tr>
                <td style="border: 1px solid black; {{ $actividad->destacada ? 'background-color: #ffc107' : '' }}">
                    {{ $actividad->unidad->nombre }}
                </td>
                <td style="border: 1px solid black; {{ $actividad->destacada ? 'background-color: #ffc107' : '' }}">
                    {{ $actividad->nombre }}
                </td>
                <td style="border: 1px solid black; {{ $actividad->destacada ? 'background-color: #ffc107' : '' }}">
                    {{ $actividad->tags }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div>
    <p class="text-center text-muted font-xs">{{ now()->isoFormat('LLLL') }}</p>
</div>
