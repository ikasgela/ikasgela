<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Text') }}</th>
            <th>{{ __('Multiple') }}</th>
            <th>{{ __('Answered') }}</th>
            <th>{{ __('Correct') }}</th>
            <th>{{ __('Image') }}</th>
            <th>{{ __('Order') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($preguntas as $pregunta)
            <tr>
                <td>{{ $pregunta->id }}</td>
                <td>{{ $pregunta->titulo }}</td>
                <td>{{ $pregunta->texto }}</td>
                <td>{!! $pregunta->multiple ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                <td>{!! $pregunta->respondida ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                <td>{!! $pregunta->correcta ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                <td>{{ $pregunta->imagen }}</td>
                <td>
                    @include('partials.botones_reordenar', ['ruta' => 'preguntas.reordenar'])
                </td>
                <td>
                    {!! Form::open(['route' => ['preguntas.destroy', $pregunta->id], 'method' => 'DELETE']) !!}
                    <div class='btn-group'>
                        <a title="{{ __('Edit') }}"
                           href="{{ route('preguntas.edit', [$pregunta->id]) }}"
                           class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                        @include('partials.boton_borrar')
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
